<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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

        // Add a title
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

        // Define table styles
        $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
        $headerStyle = ['bold' => true];
        $phpWord->addTableStyle('InventoryTable', $tableStyle);
        $table = $section->addTable('InventoryTable');

        // Add table headers
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

        // Add product data rows
        foreach ($products as $index => $product) {
            $table->addRow();
            $table->addCell(500)->addText($index + 1);
            
            // This logic will now correctly find the data regardless of small key name differences
            $table->addCell(2000)->addText($product['product_name'] ?? $product['generic_name'] ?? 'N/A');
            $table->addCell(1500)->addText($product['brand_name'] ?? 'N/A');
            $table->addCell(1000)->addText($product['strength'] ?? 'N/A');
            $table->addCell(1000)->addText($product['form'] ?? 'N/A');
            $table->addCell(800)->addText($product['quantity'] ?? 'N/A');
            $table->addCell(1500)->addText($product['batch_number'] ?? 'N/A');
            $table->addCell(1500)->addText($product['expiry_date'] ?? 'N/A');
            $table->addCell(1500)->addText($product['location'] ?? 'N/A');
        }

        // Save the file to a temporary location and send it for download
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'RMPOIMS_Inventory_' . date('Y-m-d_H-i-s') . '.docx';
        
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}