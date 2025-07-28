<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\High; 
use Illuminate\Support\Facades\App;


class QrCodeController extends Controller
{
    public function showOrderQrCode(Order $order)
    {
        // ✅ Fetch the user's location
        $locationName = $order->user->company->location ? $order->user->company->location->province : 'Unknown';
        $locationId = $order->user->company->location->id ?? 'Unknown';

        // ✅ Fetch the exclusive deal from the order
        $deal = $order->exclusive_deal;
        $qrData = [];

        if ($deal && $deal->product) {
            $product = $deal->product;

            // ✅ Find the inventory entry for this product (FIFO + FEFO)
            $inventory = $product->inventories()
                ->where('location_id', $locationId) // Filter by user's location
                ->where('quantity', '>', 0) // Ensure there's stock
                ->orderBy('expiry_date', 'asc')  // First, get the nearest expiry
                ->orderBy('created_at', 'asc')  // Then, get the oldest acquired
                ->first();

            // ✅ Get the price from `exclusive_deal`
            $unitPrice = $deal->price ?? 0;
            $totalPrice = $unitPrice * $order->quantity;

            if ($inventory) {
                // ✅ Prepare JSON data for the QR code
                $qrData = [
                    'order_id'      => $order->id,
                    'user_name'     => $order->user->name,
                    'company_name'  => $order->user->company->name ?? 'No Company',
                    'company_address' => $order->user->company->address ?? 'No Address',
                    'location'      => $locationName,
                    'product_name'  => $product->generic_name,
                    'brand_name'    => $product->brand_name,
                    'form'          => $product->form,
                    'strength'          => $product->strength,
                    'batch_number'  => $inventory->batch_number,
                    'expiry_date'   => $inventory->expiry_date,
                    'date_acquired' => $inventory->created_at->format('Y-m-d'),
                    'quantity'      => $order->quantity,
                    'unit_price'    => number_format($unitPrice, 2), // ✅ Get price from `exclusive_deal`
                    'total_price'   => number_format($totalPrice, 2) // ✅ Calculate total price
                ];
            } else {
                $qrData = [
                    'order_id'      => $order->id,
                    'user_name'     => $order->user->name,
                    'company_name'  => $order->user->company->name ?? 'No Company',
                    'company_address' => $order->user->company->address ?? 'No Address',
                    'location'      => $locationName,
                    'product_name'  => $product->generic_name,
                    'status'        => 'Out of Stock',
                    'unit_price'    => number_format($unitPrice, 2),
                    'total_price'   => 'N/A'
                ];
            }
        }

        // ✅ Convert data to JSON for the QR code
        $qrContent = json_encode($qrData, JSON_PRETTY_PRINT);

        // ✅ Generate the QR Code
        $qrCode = new QrCode($qrContent);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $pngData = $result->getString();

       $subfolder = 'qrcodes';

if (App::environment('local')) {
    // Localhost (e.g., Laragon/XAMPP)
    $directory = public_path($subfolder);
    $filePath = $directory . "/order_{$order->id}.png";
} else {
    // Production (e.g., Hostinger)
    $directory = base_path('../public_html/' . $subfolder);
    $filePath = $directory . "/order_{$order->id}.png";
}

// ✅ Ensure the directory exists
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

// ✅ Save the QR code file
file_put_contents($filePath, $pngData);
            
            Order::updateOrCreate(
                ['id' => $order->id],
                ['qr_code' => $filePath]
            );

            // 10) Build a public URL for display
            $qrCodeUrl = asset("qrcodes/order_{$order->id}.png");

            // 11) Return a Blade view that shows the QR code
            return view('orders.show_qrcode', [
                'order'     => $order,
                'qrCodeUrl' => $qrCodeUrl
            ]);
    }
}
