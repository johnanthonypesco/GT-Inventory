<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\ImmutableHistory;
use App\Models\Inventory;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\InventoryExport;
use App\Models\Order;
use App\Models\Product;
use Str;

class ExportController extends Controller
{
    public function export(Request $request, 
    $exportType = 'all', 
    $exportSpecification = null, 
    $secondaryExportSpecification = null,
    ){

        $validated = $request->validate([
            'array' => 'nullable',

            // universal filters
            'province_filter' => 'nullable|string',            
            'date_filter_start' => 'nullable|date',
            'date_filter_end' => 'nullable|date',
            
            // exclusive inventory filters
            'batch_filter' => 'nullable|string',

            // exclusive order filters
            'employee_search' => 'nullable|string',
            'company_filter' => 'nullable|string',
            'product_filter' => 'nullable',
            'status_filter' => 'nullable|string',
            'po_filter' => 'nullable|string',
        ]);

        // $validated = array_map('strip_tags', $validated);
        
        // universal filters
        $province_filter = $request->input('province_filter') ?? null;
        $date_filter_start = $request->input('date_filter_start') ?? null;
        $date_filter_end = $request->input('date_filter_end') ?? null;

        // exclusive inventory filters
        $batch_filter = $request->input('batch_filter') ?? null;

        // exclusive order filters
        $status_filter = $request->input('status_filter') ?? null;
        $employee_search = $request->input('employee_search') ?? null;
        $company_filter = $request->input('company_filter') ?? null;
        $product_filter = $request->input('product_filter') ?? null;
        $po_filter = $request->input('po_filter') ?? null;

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
                ]);
                
                $orders = $orders->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereHas('user.company.location', function ($query) use ($exportSpecification) {
                    $query->where('province', $exportSpecification);
                })
                ->orderByDesc('date_ordered')
                ->get()
                ->groupBy('status');

                $word = '[PENDING & PACKED & OUT FOR DELIVERY]';
                $fileName = $exportSpecification . '-orders-[' . date('Y-m-d') . ' - ' . $word . '.xlsx';  
                break;
            
            case "immutable-export":
                $historyOrders = ImmutableHistory::whereIn('status', ['delivered', 'cancelled'])
                ->where('province', $exportSpecification);

                if (!in_array($employee_search, ['all', 'All', null], true)) {
                    $employee_search_filter = explode(' - ', $employee_search);
                    $emp_name = $employee_search_filter[0];
                    $company_name = $employee_search_filter[1];

                    $historyOrders = $historyOrders->where('employee', $emp_name)
                    ->where('company', $company_name);
                }

                if (!in_array($status_filter, ['all', 'All', null], true)) {
                    $historyOrders = $historyOrders->where('status', $status_filter);
                }

                if (!in_array($company_filter, ['all', 'All', null], true)) {
                    $historyOrders = $historyOrders->where('company', $company_filter);
                }

                if ($date_filter_start !== null && $date_filter_end !== null) {
                    $historyOrders = $historyOrders->whereBetween('date_ordered', [$date_filter_start, $date_filter_end]);
                }

                if (!in_array($product_filter, ['all', 'All', null], true)) {
                    $product = Product::findOrFail($product_filter);
                    
                    $historyOrders = $historyOrders->where('generic_name', $product->generic_name)
                    ->where('brand_name', $product->brand_name)
                    ->where('form', $product->form)
                    ->where('strength', $product->strength);
                }

                if (!in_array($po_filter, ['all', 'All', null], true)) {
                    $historyOrders = $historyOrders->where('purchase_order_no', $po_filter);
                }

                $historyOrders = $historyOrders->orderByDesc('date_ordered')
                ->get()
                ->groupBy('status');

                $word = '[DELIVERED & CANCELLED]';
                $fileName = $exportSpecification . '-orders-[' . date('Y-m-d') . ' - ' . $word . '.xlsx';  
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
            // dd("normal orders here");
            $export = new InventoryExport($orders, "orders", $exportType);
        }
        
        else if ($exportType === "immutable-export" && $exportSpecification !== null) {
            // dd("immutable stuff here");
            $export = new InventoryExport($historyOrders, "immutable-orders", $exportType);
        }

        // dd($exportType);

        return Excel::download($export, $fileName);
    }
}
