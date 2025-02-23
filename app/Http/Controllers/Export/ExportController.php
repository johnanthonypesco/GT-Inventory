<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export() {
        // Initializes the writer
        $writer = WriterEntityFactory::createXLSXWriter();

        // Readies up the writer to accept data and export
        $writer->openToBrowser('export.xlsx');

        $headers = WriterEntityFactory::createRowFromArray([
            'Batch No.', 
            'Generic Name', 
            'Brand Name', 
            'Form', 
            'Stregth', 
            'Quantity', 
            'Expiry Date'
        ]);

        $writer->addRow($headers);

        $inventory = Inventory::with('product')->select()->get();

        foreach ($inventory as $stock) {
            $row = WriterEntityFactory::createRowFromArray([
                $stock->batch_number,
                $stock->product->generic_name,
                $stock->product->brand_name,
                $stock->product->form,
                $stock->product->strength,
                $stock->quantity,
                $stock->expiry_date,
            ]);

            $writer->addRow($row);
        }

        // Closes the writer and finally exports the stuff :)
        $writer->close();
    }
}
