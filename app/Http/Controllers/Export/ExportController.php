<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Location;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export($exportType = 'all') {
        // Initializes the writer
        $writer = WriterEntityFactory::createXLSXWriter();

        // Readies up the writer to accept data and export
        switch (strtolower($exportType)) {
            case 'all':
                $writer->openToBrowser('all-stocks-[' . date('Y-m-d') . '].xlsx');                
                break;
            case 'tarlac':
                $writer->openToBrowser('tarlac-stocks-[' . date('Y-m-d') . '].xlsx');                
                break;
            case 'nueva ecija':
                $writer->openToBrowser('nueva-ecija-stocks-[' . date('Y-m-d') . '].xlsx');                
                break;
        }

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

        switch (strtolower($exportType)) {
            case 'all':
                $inventory = Inventory::with('product')->orderBy('created_at', 'desc')->get();
                break;
            case 'tarlac':
                $query = Location::where('province', 'Tarlac')->first()->id;
                $tarlacID = $query ? $query : false;
                
                $inventory = Inventory::with('product')->where('location_id', $tarlacID)->get() 
                ? Inventory::with('product')->where('location_id', $tarlacID)->orderBy('created_at', 'desc')->get()
                : Inventory::with('product')->orderBy('created_at', 'desc')->get();
                break;
            case 'nueva ecija':
                $query = Location::where('province', 'Nueva Ecija')->first()->id;
                $nuevaID = $query ? $query : false;
                
                $inventory = Inventory::with('product')->where('location_id', $nuevaID)->get() 
                ? Inventory::with('product')->where('location_id', $nuevaID)->orderBy('created_at', 'desc')->get()
                : Inventory::with('product')->orderBy('created_at', 'desc')->get();
                break;
        }

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
