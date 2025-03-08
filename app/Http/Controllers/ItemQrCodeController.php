<?php

namespace App\Http\Controllers;

use App\Models\OrderItem; // Or wherever your "item" model is
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\Storage;

class ItemQrCodeController extends Controller
{
    public function showItemQrCode($id)
    {
        // 1. Fetch the order item (this might differ based on your structure)
        $item = OrderItem::with('exclusive_deal.product')->findOrFail($id);

        // 2. Build text for the QR code (customize as needed)
        $qrContent = "Item ID: {$item->id}\n"
                   . "Product: {$item->exclusive_deal->product->generic_name}\n"
                   . "Brand: {$item->exclusive_deal->product->brand_name}\n"
                   . "Quantity: {$item->quantity}\n";

        // 3. Generate QR code
        $qrCode = QrCode::create($qrContent)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0)) // black
            ->setBackgroundColor(new Color(255, 255, 255)); // white

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // 4. Optional: Save the QR code (so you don't generate it every time)
        $filePath = "qrcodes/item_{$item->id}.png";
        Storage::put("public/{$filePath}", $result->getString());

        // Alternatively, you can generate it on-the-fly (inline image) if you don't need to store it.
        // For this example, we store it:
        $qrCodeUrl = asset("storage/{$filePath}");

        // 5. Return a view that shows the QR code + print button
        return view('items.show_qrcode', compact('item', 'qrCodeUrl'));
    }
}
