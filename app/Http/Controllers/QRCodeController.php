<?php
// use Endroid\QrCode\QrCode;
// use Endroid\QrCode\Writer\PngWriter;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\ErrorCorrectionLevel;
// use Endroid\QrCode\Color\Color;
// use Illuminate\Support\Facades\Storage;

// function generateOrderQrCode($order)
// {
//     // Prepare the content you want in the QR code
//     $orderDetails = "Order ID: {$order->id}\nUser: {$order->user->name}\n";
//     foreach ($order->exclusive_deal as $deal) {
//         // If each deal has a unique quantity, replace $order->quantity with $deal->quantity
//         $orderDetails .= "Product: {$deal->product->generic_name} - Quantity: {$order->quantity}\n";
//     }

//     // Create the QR code
//     $qrCode = QrCode::create($orderDetails)
//         ->setEncoding(new Encoding('UTF-8'))
//         ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
//         ->setSize(300)
//         ->setMargin(10)
//         ->setForegroundColor(new Color(0, 0, 0))       // black
//         ->setBackgroundColor(new Color(255, 255, 255)); // white

//     // Write the QR code as a PNG
//     $writer = new PngWriter();
//     $result = $writer->write($qrCode);

//     // Save the QR code image to the storage folder (public disk)
//     $path = "qrcodes/order_{$order->id}.png";
//     Storage::put("public/{$path}", $result->getString());

//     // Update the order's `qr_code` column with the path
//     $order->update(['qr_code' => $path]);

//     // Return the public URL to the QR code image
//     return asset("storage/{$path}");
// }




// namespace App\Http\Controllers;

// use App\Models\Order;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;

// // Endroid QR Code classes
// use Endroid\QrCode\Builder\Builder;
// use Endroid\QrCode\Writer\PngWriter;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\ErrorCorrectionLevel\High; // or use Endroid\QrCode\ErrorCorrectionLevel
// use Endroid\QrCode\Color\Color;

// class QrCodeController extends Controller
// {
//     public function showOrderQrCode(Order $order)
//     {
//         // 1. Build the text content for the QR code
//         $qrContent = "Order ID: {$order->id}\n"
//                    . "User: {$order->user->name}\n";

//         // If each order references exactly one exclusive_deal:
//         $deal = $order->exclusive_deal; 
//         if ($deal && $deal->product) {
//             $qrContent .= "Product: {$deal->product->generic_name}\n"
//                         . "Quantity: {$order->quantity}\n";
//         }

//         // 2. Generate the QR code using the Builder
//         $result = Builder::create()
//             ->writer(new PngWriter())                       // Use PNG writer
//             ->data($qrContent)                              // The text to encode
//             ->encoding(new Encoding('UTF-8'))               // Character encoding
//             ->errorCorrectionLevel(new High())              // High error correction
//             ->size(300)                                     // Pixel size
//             ->margin(10)                                    // White space around QR
//             ->foregroundColor(new Color(0, 0, 0))           // Black
//             ->backgroundColor(new Color(255, 255, 255))     // White
//             ->build();

//         // 3. Convert the QR code image to a string
//         $pngData = $result->getString();

//         // 4. Decide where to store the file
//         $filePath = "qrcodes/order_{$order->id}.png";

//         // 5. Save the file in storage/app/public/qrcodes/
//         Storage::put("public/{$filePath}", $pngData);

//         // 6. Update the order's `qr_code` column with the path
//         $order->update(['qr_code' => $filePath]);

//         // 7. Build a public URL for display
//         $qrCodeUrl = asset("storage/{$filePath}");

//         // 8. Return a Blade view that shows the QR code
//         return view('orders.show_qrcode', [
//             'order'     => $order,
//             'qrCodeUrl' => $qrCodeUrl
//         ]);
//     }
// }


    namespace App\Http\Controllers;

    use App\Models\Order;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

    // Endroid QR Code (v6) classes
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;
    use Endroid\QrCode\ErrorCorrectionLevel\High; 

    class QrCodeController extends Controller
    {
        public function showOrderQrCode(Order $order)
        {
            // 1) Build the text content for the QR code
           

         // Fetch the user's location
$locationName = $order->user->location ? $order->user->location->province : 'Unknown Location';

// Start building QR content
$qrContent = "Order ID: {$order->id}\n"
           . "User: {$order->user->name}\n"
           . "Location: {$locationName}\n";

// Fetch the product from the order
$deal = $order->exclusive_deal;
if ($deal && $deal->product) {
    $product = $deal->product;

    // Find the inventory entry for this product (FIFO + FEFO)
    $inventory = $product->inventories()
                         ->where('quantity', '>', 0) // Ensure there's stock
                         ->orderBy('expiry_date', 'asc')  // First, get the nearest expiry
                         ->orderBy('created_at', 'asc')  // Then, get the oldest acquired
                         ->first();

                         if ($inventory) {
                            // Prepare JSON data for the QR code
                            $qrData = [
                                'order_id'      => $order->id,
                                'user_name'     => $order->user->name,
                                'location'      => $order->user->company->location ? $order->user->company->location->province : 'Unknown',
                                'product_name'  => $product->generic_name,
                                'batch_number'  => $inventory->batch_number,
                                'expiry_date'   => $inventory->expiry_date,
                                'date_acquired' => $inventory->created_at->format('Y-m-d'),
                                'quantity'      => $order->quantity
                            ];
                        } else {
                            $qrData = [
                                'order_id'      => $order->id,
                                'user_name'     => $order->user->name,
                                'location'      => $order->user->location ? $order->user->location->province : 'Unknown',
                                'product_name'  => $product->generic_name,
                                'status'        => 'Out of Stock'
                            ];
                        }
                        
                        // Convert data to JSON for the QR code
                        $qrContent = json_encode($qrData, JSON_PRETTY_PRINT);
                        
}


            // 2) Instantiate QrCode (constructor style)
            $qrCode = new QrCode($qrContent);

            // 3) Set QR code properties
            //    (In v6, setDimensions(width, height) is the recommended approach,
            //     but setSize() may still work in some older v6 releases.)
            // $qrCode->setDimensions(300, 300);
            // $qrCode->setMargin(10);

            // $qrCode->setEncoding('UTF-8');
            // $qrCode->setErrorCorrectionLevel(new High());

            // // 4) Optional colors
            // $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
            // $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);

            // 5) Create a PNG writer and write the QR code
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // 6) Get the PNG data as a string
            $pngData = $result->getString();

            // 7) Decide on a file path
            $filePath = "qrcodes/order_{$order->id}.png"; // Correct path

            // Save the QR code file in "storage/app/public/qrcodes/"
            Storage::disk('public')->put($filePath, $pngData);
            
            // 9) Instead of $order->update(...), use updateOrCreate
            //    If an order with this ID doesn't exist, it creates one.
            //    If it does, it updates the existing record's "qr_code" field.
            //    Usually we already have $order, so this is somewhat redundant, but here you go:
            Order::updateOrCreate(
                ['id' => $order->id],
                ['qr_code' => $filePath]
            );

            // 10) Build a public URL for display
            $qrCodeUrl = asset("storage/{$filePath}");

            // 11) Return a Blade view that shows the QR code
            return view('orders.show_qrcode', [
                'order'     => $order,
                'qrCodeUrl' => $qrCodeUrl
            ]);
        }
    }
