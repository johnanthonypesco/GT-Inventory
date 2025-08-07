<?php

// namespace App\Http\Controllers\mobile;

// use App\Http\Controllers\Controller;
// use App\Http\Controllers\Admin\HistorylogController; // Ensure you have this
// use App\Models\ImmutableHistory;
// use App\Models\Inventory;
// use App\Models\Location;
// use App\Models\Order;
// use App\Models\Product;
// use App\Models\ScannedQrCode;
// use Carbon\Carbon;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Storage;

// class MobileStaffQrController extends Controller
// {
//     /**
//      * Process a scanned QR code from the mobile app, deduct inventory, and save the signature.
//      * This method is based on the robust logic from the web InventoryController.
//      */
//     public function deductInventoryFromScan(Request $request)
//     {
//         // A database transaction ensures all queries succeed or none do.
//         DB::beginTransaction();
        
//         $orderId = $request->input('order_id'); // Get order_id for logging even on failure

//         try {
//             // ✅ Step 1: Validate incoming data from the mobile app
//             $validatedData = $request->validate([
//                 'order_id'      => 'required|integer|exists:orders,id',
//                 'product_name'  => 'required|string',
//                 'brand_name'    => 'nullable|string',
//                 'strength'      => 'required|string',
//                 'form'          => 'required|string',
//                 'location'      => 'required|string',
//                 'quantity'      => 'required|integer|min:1',
//                 'signature'     => 'required|image|mimes:png|max:2048', // 2MB limit
//             ]);

//             Log::info("Initiating mobile inventory deduction for Order ID: {$orderId}", $validatedData);

//             // ✅ Step 2: Perform initial checks
//             if (ScannedQrCode::where('order_id', $orderId)->exists()) {
//                 throw new \Exception('This QR code has already been scanned!');
//             }

//             // Find the Location ID
//             $locationId = Location::where('province', $validatedData['location'])->value('id');
//             if (!$locationId) {
//                 throw new \Exception('Location "' . $validatedData['location'] . '" not found in the database');
//             }

//             // Find the Product ID
//             $productId = Product::where('generic_name', $validatedData['product_name'])
//                 ->where('brand_name', $validatedData['brand_name'])
//                 ->where('strength', $validatedData['strength'])
//                 ->where('form', $validatedData['form'])
//                 ->value('id');
//             if (!$productId) {
//                 throw new \Exception('Product "' . $validatedData['product_name'] . '" not found in the database');
//             }

//             // ✅ Step 3: Find and lock all available batches for the product to prevent race conditions
//             $inventories = Inventory::where('location_id', $locationId)
//                 ->where('product_id', $productId)
//                 ->where('quantity', '>', 0)
//                 ->orderBy('expiry_date', 'asc') // Use oldest stock first (FIFO)
//                 ->orderBy('created_at', 'asc')
//                 ->lockForUpdate() // Lock the rows to prevent other processes from modifying them
//                 ->get();

//             $totalAvailable = $inventories->sum('quantity');
//             if ($totalAvailable < $validatedData['quantity']) {
//                 throw new \Exception('Not enough stock. Requested: ' . $validatedData['quantity'] . ', Available: ' . $totalAvailable);
//             }

//             // ✅ Step 4: Deduct from batches sequentially and create an audit trail
//             $quantityToDeduct = $validatedData['quantity'];
//             $affectedBatches = [];

//             foreach ($inventories as $inventory) {
//                 if ($quantityToDeduct <= 0) break;

//                 $deductFromThisBatch = min($inventory->quantity, $quantityToDeduct);
                
//                 $inventory->quantity -= $deductFromThisBatch;
//                 $inventory->save();
                
//                 $quantityToDeduct -= $deductFromThisBatch;
                
//                 // Record which batch was affected for the audit trail
//                 $affectedBatches[] = [
//                     'batch_number'      => $inventory->batch_number,
//                     'expiry_date'       => $inventory->expiry_date,
//                     'deducted_quantity' => $deductFromThisBatch
//                 ];
//             }

//             // ✅ Step 5: Update the order status to 'delivered'
//             Order::where('id', $orderId)->update([
//                 'status' => 'delivered',
//                 'updated_at' => now()
//             ]);

//             // ✅ Step 6: Create an immutable history record for archival
//             $orderArchiveArray = Order::with(['user.company.location', 'exclusivedeal.product'])->findOrFail($orderId)->toArray();
            
//             ImmutableHistory::create([
//                 'order_id'      => $orderId,
//                 'province'      => $orderArchiveArray['user']['company']['location']['province'],
//                 'company'       => $orderArchiveArray['user']['company']["name"],
//                 'employee'      => $orderArchiveArray['user']["name"],
//                 'date_ordered'  => Carbon::parse($orderArchiveArray["date_ordered"])->toDateString(),
//                 'status'        => 'delivered', // Status is now delivered
//                 'generic_name'  => $orderArchiveArray['exclusivedeal']['product']["generic_name"],
//                 'brand_name'    => $orderArchiveArray['exclusivedeal']['product']["brand_name"],
//                 'form'          => $orderArchiveArray['exclusivedeal']['product']["form"],
//                 'strength'      => $orderArchiveArray['exclusivedeal']['product']["strength"],
//                 'quantity'      => $orderArchiveArray["quantity"],
//                 'price'         => $orderArchiveArray['exclusivedeal']['price'],
//                 'subtotal'      => $orderArchiveArray['exclusivedeal']['price'] * $orderArchiveArray["quantity"],
//             ]);

//             // ✅ Step 7: Store the signature file
//             $signaturePath = null;
//             if ($request->hasFile('signature')) {
//                 $signature = $request->file('signature');
//                 $fileName = "signatures/signature_{$orderId}.png";
//                 Storage::disk('public')->put($fileName, file_get_contents($signature->getRealPath()));
//                 $signaturePath = $fileName;
//             }

//             // ✅ Step 8: Log the successful scan
//             ScannedQrCode::create([
//                 'order_id'         => $orderId,
//                 'product_name'     => $validatedData['product_name'],
//                 'location'         => $validatedData['location'],
//                 'quantity'         => $validatedData['quantity'],
//                 'affected_batches' => json_encode($affectedBatches),
//                 'scanned_at'       => now(),
//                 'signature'        => $signaturePath,
//             ]);
            
//             // ✅ Step 9: Finalize the transaction
//             DB::commit();

//             return response()->json(['message' => '✅ Inventory successfully deducted!'], 200);

//         } catch (\Illuminate\Validation\ValidationException $e) {
//             DB::rollBack();
//             Log::error("Validation failed for mobile scan. Order ID: {$orderId}. Error: " . $e->getMessage(), $e->errors());
//             return response()->json(['message' => '❌ Invalid data provided. ' . $e->getMessage(), 'errors' => $e->errors()], 422);
//         } catch (\Exception $e) {
//             DB::rollBack(); // This single block catches ALL other errors and safely rolls back the transaction.
//             Log::error("Mobile inventory deduction failed for Order ID: {$orderId}. Error: " . $e->getMessage());
//             return response()->json(['message' => '❌ Error: ' . $e->getMessage()], 400);
//         }
//     }
// }

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\HistorylogController; // Make sure this use statement is correct
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
use Illuminate\Validation\ValidationException;

class MobileStaffQrController extends Controller
{
    /**
     * Process a scanned QR code from the mobile app, deduct inventory, and save the signature.
     * This method aligns with the route: `/mobile/staff/process-scan`
     */
    public function processScannedOrder(Request $request)
    {
        // A database transaction ensures all queries succeed or none do.
        DB::beginTransaction();
        
        // Get order_id early for logging purposes, even if the request fails later.
        $orderId = $request->input('order_id'); 

        try {
            // ✅ Step 1: Validate incoming data from the mobile app's FormData
            $validatedData = $request->validate([
                'order_id'      => 'required|integer|exists:orders,id',
                'product_name'  => 'required|string|max:255',
                'brand_name'    => 'nullable|string|max:255',
                'strength'      => 'required|string|max:255',
                'form'          => 'required|string|max:255',
                'location'      => 'required|string|exists:locations,province',
                'quantity'      => 'required|integer|min:1',
                'signature'     => 'required|image|mimes:png|max:2048', // 2MB limit for the signature image
            ]);

            Log::info("Mobile Scan: Initiating inventory deduction for Order ID: {$orderId}", $validatedData);

            // ✅ Step 2: Perform initial checks
            if (ScannedQrCode::where('order_id', $orderId)->exists()) {
                throw new \Exception('This QR code has already been scanned and processed!');
            }

            // Find the Location ID from the province name
            $location = Location::where('province', $validatedData['location'])->first();
            if (!$location) {
                // This check is technically covered by 'exists:locations,province' validation, but it's good practice.
                throw new \Exception('Location "' . $validatedData['location'] . '" not found in the database');
            }
            $locationId = $location->id;

            // Find the Product ID
            $product = Product::where('generic_name', $validatedData['product_name'])
                ->where('brand_name', $validatedData['brand_name'])
                ->where('strength', $validatedData['strength'])
                ->where('form', 'like', '%' . $validatedData['form'] . '%') // Use 'like' for flexibility
                ->first();
            if (!$product) {
                throw new \Exception('Product "' . $validatedData['product_name'] . '" not found in the database');
            }
            $productId = $product->id;

            // ✅ Step 3: Find and lock all available batches for the product to prevent race conditions
            $inventories = Inventory::where('location_id', $locationId)
                ->where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date', 'asc') // Use oldest stock first (FIFO)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate() // Lock the rows to prevent other processes from modifying them during the transaction
                ->get();

            $totalAvailable = $inventories->sum('quantity');
            if ($totalAvailable < $validatedData['quantity']) {
                throw new \Exception('Not enough stock available. Requested: ' . $validatedData['quantity'] . ', Available: ' . $totalAvailable);
            }

            // ✅ Step 4: Deduct from batches sequentially and create an audit trail
            $quantityToDeduct = $validatedData['quantity'];
            $affectedBatches = [];

            foreach ($inventories as $inventory) {
                if ($quantityToDeduct <= 0) break;

                $deductFromThisBatch = min($inventory->quantity, $quantityToDeduct);
                
                $inventory->quantity -= $deductFromThisBatch;
                $inventory->save();
                
                $quantityToDeduct -= $deductFromThisBatch;
                
                // Record which batch was affected for the audit trail
                $affectedBatches[] = [
                    'batch_number'      => $inventory->batch_number,
                    'expiry_date'       => $inventory->expiry_date,
                    'deducted_quantity' => $deductFromThisBatch
                ];
            }

            // ✅ Step 5: Update the order status to 'delivered'
            Order::where('id', $orderId)->update([
                'status' => 'delivered',
                'updated_at' => now()
            ]);

            // ✅ Step 6: Create an immutable history record for archival
            $orderArchive = Order::with(['user.company.location', 'exclusivedeal.product'])->findOrFail($orderId);
            
            ImmutableHistory::create([
                'order_id'      => $orderId,
                'province'      => $orderArchive->user->company->location->province,
                'company'       => $orderArchive->user->company->name,
                'employee'      => $orderArchive->user->name,
                'date_ordered'  => Carbon::parse($orderArchive->date_ordered)->toDateString(),
                'status'        => 'delivered', // Status is now delivered
                'generic_name'  => $orderArchive->exclusivedeal->product->generic_name,
                'brand_name'    => $orderArchive->exclusivedeal->product->brand_name,
                'form'          => $orderArchive->exclusivedeal->product->form,
                'strength'      => $orderArchive->exclusivedeal->product->strength,
                'quantity'      => $orderArchive->quantity,
                'price'         => $orderArchive->exclusivedeal->price,
                'subtotal'      => $orderArchive->exclusivedeal->price * $orderArchive->quantity,
            ]);

            // ✅ Step 7: Store the signature file
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signatureFile = $request->file('signature');
                $fileName = "signatures/signature_{$orderId}_" . time() . ".png";
                // Use the 'public' disk which maps to storage/app/public
                $signaturePath = Storage::disk('public')->putFileAs('', $signatureFile, $fileName);
            }

            // ✅ Step 8: Log the successful scan event
            ScannedQrCode::create([
                'order_id'         => $orderId,
                'product_name'     => $validatedData['product_name'],
                'location'         => $validatedData['location'],
                'quantity'         => $validatedData['quantity'],
                'affected_batches' => json_encode($affectedBatches),
                'scanned_at'       => now(),
                'signature'        => $signaturePath,
            ]);
            
            // ✅ Step 9: Finalize the transaction
            DB::commit();

            return response()->json(['message' => '✅ Inventory successfully deducted and order marked as delivered!'], 200);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error("Validation failed for mobile scan. Order ID: {$orderId}. Error: " . $e->getMessage(), $e->errors());
            return response()->json(['message' => '❌ Invalid data provided. Please check the QR code.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // This catches all other errors (DB, logic, etc.) and safely rolls back the transaction.
            Log::error("Mobile inventory deduction failed for Order ID: {$orderId}. Error: " . $e->getMessage());
            return response()->json(['message' => '❌ Error: ' . $e->getMessage()], 400);
        }
    }
}
