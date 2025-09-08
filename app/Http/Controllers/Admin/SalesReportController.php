<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ImmutableHistory;
use App\Models\Product; // ✅ NEW: Import the Product model
use Illuminate\Http\Request;
use PDF;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $defaultStartDate = now()->subDays(7)->format('Y-m-d');
        $defaultEndDate = now()->format('Y-m-d');

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $companyId = $request->input('company_id');
        $productId = $request->input('product_id'); // ✅ NEW: Get product_id from request

        $allCompanies = Company::orderBy('name')->get();
        $allProducts = Product::orderBy('generic_name')->get(); // ✅ NEW: Fetch all products

        if ($request->hasAny(['start_date', 'end_date']) && !$request->hasAny(['download', 'preview'])) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'company_id' => 'nullable|exists:companies,id',
                'product_id' => 'nullable|exists:products,id' // ✅ NEW: Add validation for product_id
            ]);
        }

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
            'product_id' => $productId, // ✅ NEW: Pass product_id to the view
            'all_companies' => $allCompanies,
            'all_products' => $allProducts // ✅ NEW: Pass all_products to the view
        ];

        return view('admin.sales', $data);
    }

    /**
     * Generates the sales report by querying the ImmutableHistory model.
     */
    public function generateReport(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'company_id' => 'nullable|exists:companies,id',
        'product_id' => 'nullable|exists:products,id'
    ]);

    $startDate = $validated['start_date'];
    $endDate = $validated['end_date'];
    $companyId = $validated['company_id'] ?? null;
    $productId = $validated['product_id'] ?? null;
    $selectedCompany = null;
    $selectedProduct = null;

    $historyQuery = ImmutableHistory::where('status', 'delivered')
        ->whereBetween('date_ordered', [$startDate, $endDate]);

    if ($companyId) {
        $selectedCompany = Company::find($companyId);
        if ($selectedCompany) {
            $historyQuery->where('company', 'like', $selectedCompany->name);
        }
    }

    // ✅ THIS IS THE KEY SECTION THAT MAKES THE FILTER SPECIFIC
    if ($productId) {
        // 1. Find the exact product using its unique ID.
        $selectedProduct = Product::find($productId);
        
        if ($selectedProduct) {
            // 2. Use all four fields from that specific product to filter the history.
            // This guarantees it will only match sales for this exact variation.
            $historyQuery->where('generic_name', $selectedProduct->generic_name)
                         ->where('brand_name', $selectedProduct->brand_name)
                         ->where('form', $selectedProduct->form)
                         ->where('strength', $selectedProduct->strength);
        }
    }

    $histories = $historyQuery->orderBy('date_ordered', 'asc')->get();

    $companySummary = $histories->groupBy('company')->map(function ($companyHistories, $companyName) {
        return (object)[
            'name' => $companyName,
            'total_orders' => $companyHistories->count(),
            'total_sales' => $companyHistories->sum('subtotal')
        ];
    })->sortBy('name');

    $data = [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'company_id' => $companyId,
        'product_id' => $productId,
        'selected_company_name' => $selectedCompany->name ?? null,
        'selected_product' => $selectedProduct,
        'histories' => $histories,
        'company_summary' => $companySummary,
        'all_companies' => Company::orderBy('name')->get(),
        'all_products' => Product::orderBy('generic_name')->get(),
        'total_sales' => $histories->sum('subtotal')
    ];

    if ($request->has('download')) {
        $pdf = PDF::loadView('admin.reports.sales_pdf', $data);
        $companyNameSlug = $selectedCompany ? str_replace(' ', '_', $selectedCompany->name) : 'all';
        $productNameSlug = $selectedProduct ? str_replace(' ', '_', $selectedProduct->generic_name) : 'all';
        $filename = "sales_report_{$companyNameSlug}_{$productNameSlug}_{$startDate}_to_{$endDate}.pdf";
        return $pdf->download($filename);
    }

    return view('admin.sales', $data);
}
}