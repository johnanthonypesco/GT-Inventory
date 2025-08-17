<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    public function exportDocx(Request $request)
    {
        try {
            $validated = $request->validate(['products' => 'required|array|min:1']);
            $products = $validated['products'];

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // --- (The code for generating the title and table is the same as above) ---
            $section->addText('RMPOIMS - OCR Scanned Copy', ['bold' => true, 'size' => 16], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $section->addText('Generated on: ' . date('F j, Y, g:i a'), ['size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $section->addTextBreak(1);
            $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
            $headerStyle = ['bold' => true];
            $phpWord->addTableStyle('InventoryTable', $tableStyle);
            $table = $section->addTable('InventoryTable');
            $table->addRow();
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
                $table->addCell(2000)->addText(htmlspecialchars($product['product_name'] ?? 'N/A'));
                $table->addCell(1500)->addText(htmlspecialchars($product['brand_name'] ?? 'N/A'));
                $table->addCell(1000)->addText(htmlspecialchars($product['strength'] ?? 'N/A'));
                $table->addCell(1000)->addText(htmlspecialchars($product['form'] ?? 'N/A'));
                $table->addCell(800)->addText(htmlspecialchars($product['quantity'] ?? 'N/A'));
                $table->addCell(1500)->addText(htmlspecialchars($product['batch_number'] ?? 'N/A'));
                $table->addCell(1500)->addText(htmlspecialchars($product['expiry_date'] ?? 'N/A'));
                $table->addCell(1500)->addText(htmlspecialchars($product['location'] ?? 'N/A'));
            }
            // --- (End of table generation) ---

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

            // 1. Create a temporary local file to hold the document
            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $objWriter->save($tempFile);
            
            // 2. SAVE: Define server path and save the file to public storage
            $datePath = Carbon::now()->format('Y/m/d');
            $fileName = 'RMPOIMS_Inventory_' . date('Y-m-d_H-i-s') . '.docx';
            $fullPath = 'receipts/docs_ocr_copy/' . $datePath . '/' . $fileName;
            Storage::disk('public')->put($fullPath, file_get_contents($tempFile));

            // 3. DOWNLOAD: Send the temporary file to the browser for download.
            // Laravel will automatically delete the temp file after the download is complete.
            return response()->download($tempFile, $fileName);

        } catch (Exception $e) {
            Log::error('DOCX Export Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not generate the document due to a server error.'], 500);
        }
    }
}