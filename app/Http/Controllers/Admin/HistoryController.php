<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImmutableHistory;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
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

        // dd(session('order-type'));
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

            'current_filters' => [
                'search' => $employeeSearch,
                'location' => $orderProvinceFilter,
                'status' => $orderStatusFilter,
            ],
            
            'customersSearchSuggestions' => ImmutableHistory::select(['employee', 'company'])->distinct()->get(),
        ]);
    }
}
