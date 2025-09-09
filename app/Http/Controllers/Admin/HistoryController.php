<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ImmutableHistory;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    //  
    public function showHistory(Request $request){
        $employeeSearch = $request->input('employee_search', null);
        $orderProvinceFilter = $request->input('province_filter', 'all');
        $orderStatusFilter = $request->input('status_filter', 'all');
        $orderDateFilter = $request->input('date_filter', 'all');
        $orderProductFilter = $request->input('product_filter', 'all');
        $companyFilter = $request->input('company_filter', 'all');
        $poFilter = $request->input('po_filter', 'all');

        $orders = ImmutableHistory::with(['ScannedQrCode']);

        // If may search then add another gyad damn condition
        if ($employeeSearch !== null) {
            $employeeSearch = explode(' - ', $employeeSearch);

            // Assumes format is "EmployeeName - CompanyName"
            $employeeName = $employeeSearch[0] ?? null;
            $companyName = $employeeSearch[1] ?? null;

            $orders = $orders->where('employee', $employeeName)
            ->where('company', $companyName);
        }
        
        if ($companyFilter !== 'all') {
            $orders = $orders->where('company', $companyFilter);
        }

        if ( $orderDateFilter !== 'all' && $orderDateFilter[0] !== null) {
            $orders = $orders->whereBetween('date_ordered', 
                [$orderDateFilter[0], $orderDateFilter[1] ?? Carbon::today()->format('Y-m-d')]
            );

            if ($orderDateFilter[1] === null) {
                $orderDateFilter[1] = Carbon::today()->format('Y-m-d');
            }
        }

        if ($orderProductFilter !== 'all') {
            $product = Product::findOrFail($orderProductFilter);

            $orders = $orders->where('generic_name', $product->generic_name)
            ->where('brand_name', $product->brand_name)
            ->where('form', $product->form)
            ->where('strength', $product->strength);
        }
        
        if ($poFilter !== 'all' && $poFilter !== null) {
            $orders = $orders->where('purchase_order_no', $poFilter);
        }

        switch ($orderStatusFilter) {
            case "delivered" :
                $orders = $orders->where('status', 'delivered');
                break;
            case "cancelled" :
                $orders = $orders->where('status', 'cancelled');
                break;
            default:
                $orders = $orders->whereIn('status', ['delivered', 'cancelled']);
                break;
        }

        if ($orderProvinceFilter !== "all") {
            $orders = $orders->where('province', $orderProvinceFilter);
        }

        $orders = $orders->orderByDesc('date_ordered')
        ->get();

        // Explanation ng Hierchy: Province>Company>(we’ll slice per-company here)>employee+date>status>order
        
        // If curious ka kung pano gumagana pagination neto, checl mo nalang yung sa OrderController, nandun
        // yung mga explanations, ni-copy paste ko lang dito yun & ginawa kong compatible sa table structure
        // ng ImmutableHistory table
        //
        // by: Seagray
        
        $provinces = $orders
        ->groupBy(fn($o) => $o->province)
        ->map(function($provinceOrders) use ($request) {
            $perPage = 10;

            return $provinceOrders
                ->groupBy(fn($o) => $o->company)
                ->mapWithKeys(function($companyOrders, $companyName) use ($request, $perPage) {
                    $slug      = Str::slug($companyName, '_');
                    $pageParam = "page_{$slug}";
                    $current   = (int) $request->input($pageParam, 1);

                    $slice = $companyOrders->forPage($current, $perPage);

                    $paginator = new LengthAwarePaginator(
                        $slice->values(),
                        $companyOrders->count(),
                        $perPage,
                        $current,
                        [
                            'pageName' => $pageParam,
                            'path'     => LengthAwarePaginator::resolveCurrentPath(),
                            'query'    => Arr::except($request->query(), $pageParam),
                        ]
                    );

                    $grouped = $slice
                        ->groupBy(fn($o) => $o->employee . '|' . $o->date_ordered)
                        ->map(fn($empOrders) =>
                            $empOrders->groupBy(fn($o) => $o->status)
                        );

                    $grouped->paginator = $paginator;

                    return [ $companyName => $grouped ];
                });
        });

        return view('admin.history', [
            "provinces" => $provinces,

            "dropdownLocationOptions" => Location::get()->pluck('province'),
            "dropDownCompanyOptions" => Company::all()->sortBy('name'),
            "dropDownProductOptions" => Product::all()->sortBy('generic_name'),

            'current_filters' => [
                'search' => $employeeSearch,
                'company' => $companyFilter,
                'location' => $orderProvinceFilter,
                'status' => $orderStatusFilter,
                'date' => [
                    "start" => $orderDateFilter[0],
                    "end" => $orderDateFilter[1]
                ],
                'product' => $orderProductFilter,
                'po' => $poFilter,
            ],
            
            'customersSearchSuggestions' => ImmutableHistory::select(['employee', 'company'])->distinct()->get(),
        ]);
    }
}
