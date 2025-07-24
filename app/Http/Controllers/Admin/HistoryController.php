<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImmutableHistory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    //  
    public function showHistory(Request $request){
        // $orders = ImmutableHistory::with('ScannedQrCode') 
        //     ->whereIn('status', ['delivered', 'cancelled'])
        //     ->orderBy('date_ordered', 'desc')
        //     ->get()
        // // groups orders by province
        // ->groupBy(function ($orders)  { 
        //     return $orders->province;
        // })
        // // groups the province orders by company name
        // ->map(function ($provinces) { 
        //     return $provinces->groupBy(function ($orders) {
        //         return $orders->company;
        //     });
        // })
        // // groups the company name orders by employee name & order date
        // ->map(function ($provinces) { 
        //     return $provinces->map(function ($companies) {
        //         return $companies->groupBy(function ($orders) {
        //             return $orders->employee . '|' . $orders->date_ordered;
        //         });
        //     });
        // })
        // //  groups the employee name & order date orders by status
        // ->map(function ($provinces) {
        //     return $provinces->map(function ($companies) {
        //         return $companies->map(function ($employees) {
        //             return $employees->groupBy(function ($orders) {
        //                 return $orders->status;
        //             });
        //         });
        //     });
        // });

        $employeeSearch = $request->input('employee_search', null);
        
        $orders = ImmutableHistory::with(['ScannedQrCode']);
        
        // If may search then add another gyad damn condition
        // if ($employeeSearch !== null) {
        //     $employeeSearch = explode(' - ', $employeeSearch);

        //     $orders = $orders->whereHas('user', function ($query) use ($employeeSearch) {
        //         $query->where('name', $employeeSearch[0])
        //             ->whereHas('company', function ($q) use ($employeeSearch) {
        //                 $q->where('name', $employeeSearch[1]);
        //             });
        //     });
        // }

        $orders = $orders->whereIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered','desc')
        ->get();

        // Explanation ng Hierchy: Province>Company>(we’ll slice per-company here)>employee+date>status>order
        $provinces = $orders
        // Group all orders by province
        ->groupBy(fn($o) => $o->province)
        ->map(function($provinceOrders) use ($request) {
            $perPage = 10;

            // Within each province, group by company
            return $provinceOrders
                ->groupBy(fn($o) => $o->company)
                ->mapWithKeys(function($companyOrders, $companyName) use ($request, $perPage) {
                    // --- PAGINATION SETUP ---
                    $slug      = Str::slug($companyName, '_');
                    $pageParam = "page_{$slug}";
                    $current   = (int) $request->input($pageParam, 1);

                    // Slice only this page’s worth of orders
                    $slice = $companyOrders->forPage($current, $perPage);

                    // Build the paginator for this company
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

                    // --- DEEP RE‑GROUP on the sliced orders ---
                    $grouped = $slice
                        ->groupBy(fn($o) => $o->employee . '|' . $o->date_ordered)
                        ->map(fn($empOrders) =>
                            $empOrders->groupBy(fn($o) => $o->status)
                        );

                    // Attach the paginator so your Blade can call ->links()
                    $grouped->paginator = $paginator;

                    // Return [ CompanyName => grouped(Employee|Date → Status) ]
                    return [ $companyName => $grouped ];
                });
        });

        return view('admin.history', [
            "provinces" => $provinces,
        ]);
    }
}
