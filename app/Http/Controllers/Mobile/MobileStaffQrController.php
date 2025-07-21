<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Models\ImmutableHistory;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\ScannedQrCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MobileStaffQrController extends Controller
{
    public function processScannedOrder(Request $request)
    {
        DB::beginTransaction();
        $orderId = null; 

        try {
            $qrDataString = $request->input('qr_data');
            if (!$qrDataString) throw new \Exception('QR data is missing.');
            
            $scannedData = json_decode($qrDataString, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception('Invalid QR data format.');

            $orderId = $scannedData['order_id'] ?? null;
            $productName = $scannedData['product_name'] ?? null;
            $location = $scannedData['location'] ?? null;
            $quantity = $scannedData['quantity'] ?? 1;
            $signature = $request->file('signature');

            if (ScannedQrCode::where('order_id', $orderId)->exists()) {
                throw new \Exception('This QR code has already been scanned!');
            }

            $locationId = Location::where('province', $location)->value('id');
            if (!$locationId) throw new \Exception('Location "' . $location . '" not found');

            $productId = Product::where('generic_name', $productName)->value('id');
            if (!$productId) throw new \Exception('Product "' . $productName . '" not found');

            $inventories = Inventory::where('location_id', $locationId)
                ->where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date', 'asc')->orderBy('created_at', 'asc')
                ->lockForUpdate()->get();

            if ($inventories->sum('quantity') < $quantity) {
                throw new \Exception('Not enough stock. Requested: ' . $quantity . ', Available: ' . $inventories->sum('quantity'));
            }

            $quantityToDeduct = $quantity;
            $affectedBatches = [];
            foreach ($inventories as $inventory) {
                if ($quantityToDeduct <= 0) break;
                $deductFromThisBatch = min($inventory->quantity, $quantityToDeduct);
                $inventory->quantity -= $deductFromThisBatch;
                $inventory->save();
                $quantityToDeduct -= $deductFromThisBatch;
                $affectedBatches[] = [ 'batch_number' => $inventory->batch_number, 'expiry_date' => $inventory->expiry_date, 'deducted_quantity' => $deductFromThisBatch ];
            }

            Order::where('id', $orderId)->update(['status' => 'delivered', 'updated_at' => now()]);

            $orderArchiveArray = Order::with(['user.company.location', 'exclusive_deal.product'])->findOrFail($orderId)->toArray();
            ImmutableHistory::createOrFirst([
                'order_id' => $orderId, 'province' => $orderArchiveArray['user']['company']['location']['province'], 'company' => $orderArchiveArray['user']['company']["name"],
                'employee' => $orderArchiveArray['user']["name"], 'date_ordered' => Carbon::parse($orderArchiveArray["date_ordered"])->addDay()->toDateString(), 'status' => 'delivered',
                'generic_name' => $orderArchiveArray['exclusive_deal']['product']["generic_name"], 'brand_name' => $orderArchiveArray['exclusive_deal']['product']["brand_name"],
                'form' => $orderArchiveArray['exclusive_deal']['product']["form"], 'quantity' => $orderArchiveArray["quantity"], 'price' => $orderArchiveArray['exclusive_deal']['price'],
                'subtotal' => $orderArchiveArray['exclusive_deal']['price'] * $orderArchiveArray["quantity"],
            ]);

            $signaturePath = null;
            if ($signature) {
                $fileName = "signatures/signature_{$orderId}.png";
                Storage::disk('public')->put($fileName, file_get_contents($signature->getRealPath()));
                $signaturePath = $fileName;
            }

            // This is the corrected line
            $primaryBatchNumber = !empty($affectedBatches) ? $affectedBatches[0]['batch_number'] : null;

            $dataToSave = [
                'order_id' => $orderId,
                'product_name' => $productName,
                'location' => $location,
                'quantity' => $quantity,
                'affected_batches' => json_encode($affectedBatches),
                'scanned_at' => now(),
                'signature' => $signaturePath,
                'batch_number' => $primaryBatchNumber,
            ];

            ScannedQrCode::create($dataToSave);
            
            DB::commit();
            return response()->json(['message' => 'Inventory successfully deducted!'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("QR Scan Failed for Order ID: {$orderId}. Error: " . $e->getMessage());
            return response()->json(['message' => '❌ Error: ' . $e->getMessage()], 400);
        }
    }
}