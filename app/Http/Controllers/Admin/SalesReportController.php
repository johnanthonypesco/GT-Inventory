<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Order;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PDF;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Set default dates (last 7 days)
        $defaultStartDate = now()->subDays(7)->format('Y-m-d');
        $defaultEndDate = now()->format('Y-m-d');
        
        // Get dates from request or use defaults
        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $companyId = $request->input('company_id');
        
        // Get all companies for dropdown
        $allCompanies = Company::orderBy('name')->get();
        
        // If dates were submitted but not for download/preview, validate them
        if ($request->hasAny(['start_date', 'end_date']) && !$request->hasAny(['download', 'preview'])) {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'company_id' => 'nullable|exists:companies,id'
            ]);
        }
        
        // Prepare data for view
        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
            'all_companies' => $allCompanies
        ];
        
        return view('admin.sales', $data);
    }

    public function generateReport(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'company_id' => 'nullable|exists:companies,id'
    ]);

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $companyId = $request->input('company_id');

    // Unique cache key based on filters
    $cacheKey = 'sales_report_' . md5($startDate . '_' . $endDate . '_' . ($companyId ?? 'all'));

    // Cache for 10 minutes (adjust as needed)
    $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($startDate, $endDate, $companyId) {
        // Base query for orders
        $ordersQuery = Order::with(['exclusiveDeal.product', 'exclusiveDeal.company', 'user'])
            ->where('status', 'delivered')
            ->whereBetween('date_ordered', [$startDate, $endDate]);

        if ($companyId) {
            $ordersQuery->whereHas('exclusiveDeal', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            });
        }

        $orders = $ordersQuery->orderBy('date_ordered', 'asc')->get();

        // Get companies for summary
        $companiesQuery = Company::query();

        if ($companyId) {
            $companiesQuery->where('id', $companyId);
        }

        $companies = $companiesQuery->with(['exclusiveDeals.orders' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'delivered')
                      ->whereBetween('date_ordered', [$startDate, $endDate]);
            }])->get();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
            'orders' => $orders,
            'companies' => $companies,
            'all_companies' => Company::orderBy('name')->get(),
            'total_sales' => $orders->sum(function($order) {
                return $order->quantity * $order->exclusiveDeal->price;
            })
        ];
    });

    // PDF download
    if ($request->has('download')) {
        $pdf = PDF::loadView('admin.reports.sales_pdf', $data);
        $filename = $companyId 
            ? 'sales_report_'.$data['companies']->first()->name.'_'.$startDate.'_to_'.$endDate.'.pdf'
            : 'sales_report_'.$startDate.'_to_'.$endDate.'.pdf';
        return $pdf->download($filename);
    }

    return view('admin.sales', $data);
}
}