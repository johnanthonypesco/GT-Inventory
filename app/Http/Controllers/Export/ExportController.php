<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\InventoryExport;
use App\Models\Order;
use Str;

class ExportController extends Controller
{
    public function export(Request $request, $exportType = 'all', $exportSpecification = null)
    {
        $validated = $request->validate([
            'array' => 'nullable'
        ]);

        $validated = array_map('strip_tags', $validated);

        $fileName = '';
        $export = null;

        switch (strtolower($exportType)) {
            case 'all':
                $fileName = 'all-stocks-[' . date('Y-m-d') . '].xlsx';
                $inventory = Inventory::with('product')->orderBy('created_at', 'desc')->get();
                break;
            case 'tarlac':
                $tarlacID = Location::where('province', 'Tarlac')->value('id');
                $inventory = Inventory::with('product')->where('location_id', $tarlacID)
                ->orderByDesc('created_at')->get();
                $fileName = 'tarlac-stocks-[' . date('Y-m-d') . '].xlsx';
                break;
            case 'nueva ecija':
                $nuevaID = Location::where('province', 'Nueva Ecija')->value('id');
                $inventory = Inventory::with('product')->where('location_id', $nuevaID)
                ->orderByDesc('created_at')->get();
                $fileName = 'nueva-ecija-stocks-[' . date('Y-m-d') . '].xlsx';
                break;

            case 'in-summary':
                $inventory = collect(json_decode($validated['array'], true));

                $fileName = 'in-stock-[' . date('Y-m-d') . '].xlsx';
                break;

            case 'low-summary':
                $inventory = collect(json_decode($validated['array'], true));

                $fileName = 'low-stocks-[' . date('Y-m-d') . '].xlsx';
                break;

            case 'out-summary':
                $inventory = collect(json_decode($validated['array'], true));

                $fileName = 'out-of-stocks-[' . date('Y-m-d') . '].xlsx';
                break;

            case 'near-expiry-summary':
                $inventory = Inventory::with('product')
                    ->where('quantity', '>', 0)
                    ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addMonth()])
                    ->orderByDesc('expiry_date')->get()
                    ->groupBy(function ($stocks) {
                        return $stocks->location->province;
                    });
                $fileName = 'near-expiry-stocks-[' . date('Y-m-d') . '].xlsx';
                break;

            case 'expired-summary':
                $inventory = Inventory::with('product')
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '<', Carbon::now()->toDateString())
                    ->orderByDesc('expiry_date')->get()
                    ->groupBy(function ($stocks) {
                        return $stocks->location->province;
                    });
                $fileName = 'expired-stocks-[' . date('Y-m-d') . '].xlsx';                
                break;

            case 'order-export':
                $orders = Order::with([
                    'user.company.location',
                    'exclusive_deal.product'
                ])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereHas('user.company.location', function ($query) use ($exportSpecification) {
                    $query->where('province', $exportSpecification);
                })
                ->orderByDesc('date_ordered')
                ->get()
                ->groupBy('status');

                // dd($orders->toArray());

                $fileName = $exportSpecification . '-orders-[' . date('Y-m-d') . '].xlsx';  
                break;

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
        // dd($inventory->toArray());

        if(in_array( Str::lower($exportType), ['all', 'tarlac', 'nueva ecija', 'near-expiry-summary', 'expired-summary'])) {
            $export = new InventoryExport($inventory, "individual", $exportType);
        } 
        
        else if(in_array(Str::lower($exportType), ['in-summary', 'low-summary', 'out-summary'])) {
            $export = new InventoryExport($inventory, "grouped", $exportType);
        } 

        else if ($exportType === "order-export" && $exportSpecification !== null) {
            $export = new InventoryExport($orders, "orders", $exportType);
        }

        // dd($exportType);

        return Excel::download($export, $fileName);
    }
}
