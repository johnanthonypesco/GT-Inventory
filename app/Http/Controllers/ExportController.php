<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\App; // Idagdag ito
use Illuminate\Support\Facades\File; // Idagdag ito

class ExportController extends Controller
{
    public function exportDocx(Request $request)
{
    $validated = $request->validate([
        'products' => 'required|array|min:1',
    ]);

    $products = $validated['products'];
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    // Title and table generation
    $section->addText(
        'RMPOIMS - Inventory List',
        ['bold' => true, 'size' => 16],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );
    $section->addText(
        'Generated on: ' . date('F j, Y, g:i a'),
        ['size' => 10],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );
    $section->addTextBreak(1);

    $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
    $phpWord->addTableStyle('InventoryTable', $tableStyle);
    $table = $section->addTable('InventoryTable');
    
    $table->addRow();
    $headerStyle = ['bold' => true];
    $table->addCell(500)->addText('#', $headerStyle);
    $table->addCell(2000)->addText('Product Name', $headerStyle);
    $table->addCell(1500)->addText('Brand', $headerStyle);
    $table->addCell(1000)->addText('Strength', $headerStyle);
    $table->addCell(1000)->addText('Form', $headerStyle);
    $table->addCell(800)->addText('Qty', $headerStyle);
    $table->addCell(1500)->addText('Batch No.', $headerStyle);
    $table->addCell(1500)->addText('Expiry', $headerStyle);
    $table->addCell(1500)->addText('Location', $headerStyle);

    foreach ($products as $index => $product) {
        $table->addRow();
        $table->addCell(500)->addText($index + 1);
        $table->addCell(2000)->addText($product['product_name'] ?? 'N/A');
        $table->addCell(1500)->addText($product['brand_name'] ?? 'N/A');
        $table->addCell(1000)->addText($product['strength'] ?? 'N/A');
        $table->addCell(1000)->addText($product['form'] ?? 'N/A');
        $table->addCell(800)->addText($product['quantity'] ?? 'N/A');
        $table->addCell(1500)->addText($product['batch_number'] ?? 'N/A');
        $table->addCell(1500)->addText($product['expiry_date'] ?? 'N/A');
        $table->addCell(1500)->addText($product['location'] ?? 'N/A');
    }

    // ======================================================================
    // === HOSTINGER-COMPATIBLE FILE SAVING LOGIC ===
    // ======================================================================
    
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

    // 1. Define the relative subfolder and filename
    $subfolder = 'receipts/docs_ocr_copy/' . Carbon::now()->format('Y/m/d');
    $fileName = 'RMPOIMS_Inventory_' . date('Y-m-d_H-i-s') . '.docx';

    // 2. Determine the absolute target directory based on environment
    if (App::environment('local')) {
        $targetDir = public_path($subfolder);
    } else {
        // For production (Hostinger), save to 'public_html'
        $targetDir = base_path('../public_html/' . $subfolder);
    }

    // 3. Create the directory if it doesn't exist
    if (!File::exists($targetDir)) {
        File::makeDirectory($targetDir, 0755, true, true);
    }

    // 4. Define the full absolute path for saving the file
    $fullAbsolutePath = $targetDir . '/' . $fileName;

    // 5. Save the document directly to the final destination
    $objWriter->save($fullAbsolutePath);

    // 6. The path for the JSON response should be a publicly accessible URL
    $publicPath = asset($subfolder . '/' . $fileName);

    // ======================================================================

    // 7. Return a JSON success response to the frontend.
    return response()->json([
        'success' => true,
        'message' => 'Document successfully archived on the server!',
        'path' => $publicPath
    ]);
}
}