<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ImmutableHistory; // Changed from Order
use Illuminate\Http\Request;
use PDF;

class SalesReportController extends Controller
{
    // The index method remains the same, as it only sets up the form.
    public function index(Request $request)
    {
        $defaultStartDate = now()->subDays(7)->format('Y-m-d');
        $defaultEndDate = now()->format('Y-m-d');
        
        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $companyId = $request->input('company_id');
        
        $allCompanies = Company::orderBy('name')->get();
        
        if ($request->hasAny(['start_date', 'end_date']) && !$request->hasAny(['download', 'preview'])) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'company_id' => 'nullable|exists:companies,id'
            ]);
        }
        
        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
            'all_companies' => $allCompanies
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
            'company_id' => 'nullable|exists:companies,id'
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $companyId = $validated['company_id'] ?? null;
        $selectedCompany = null;

        // Base query for immutable history
        $historyQuery = ImmutableHistory::where('status', 'delivered')
            ->whereBetween('date_ordered', [$startDate, $endDate]);

        // Filter by company if an ID is provided
        if ($companyId) {
            $selectedCompany = Company::find($companyId);
            if ($selectedCompany) {
                // Query by the company's name, as stored in ImmutableHistory
                $historyQuery->where('company', $selectedCompany->name);
            }
        }

        $histories = $historyQuery->orderBy('date_ordered', 'asc')->get();

        // Generate the company summary from the fetched histories
        $companySummary = $histories->groupBy('company')->map(function ($companyHistories, $companyName) {
            return (object)[
                'name' => $companyName,
                'total_orders' => $companyHistories->count(),
                'total_sales' => $companyHistories->sum('subtotal')
            ];
        })->sortBy('name');

        // Prepare data for the view
        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
            'selected_company_name' => $selectedCompany->name ?? null,
            'histories' => $histories, // Replaces 'orders'
            'company_summary' => $companySummary, // Replaces 'companies' for summary
            'all_companies' => Company::orderBy('name')->get(),
            'total_sales' => $histories->sum('subtotal')
        ];

        // PDF download logic
        if ($request->has('download')) {
            $pdf = PDF::loadView('admin.reports.sales_pdf', $data);
            $companyNameSlug = $selectedCompany ? str_replace(' ', '_', $selectedCompany->name) : 'all';
            $filename = 'sales_report_' . $companyNameSlug . '_' . $startDate . '_to_' . $endDate . '.pdf';
            return $pdf->download($filename);
        }

        return view('admin.sales', $data);
    }
}