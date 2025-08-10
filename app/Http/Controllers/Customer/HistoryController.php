<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\ImmutableHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function showHistory(Request $request)
    {
        $searchFilter = $request->input('search_filter', null);
        $searchFilter = str_replace(['₱', ','], '', $searchFilter);
        $searchFilter = $searchFilter ? explode(' - ', $searchFilter) : null;
        
        $statusFilter = $request->input('status_filter', 'all');
        $pageNum = 10;
        $user = auth('web')->user();

        // Start the query
        $ordersQuery = ImmutableHistory::where('employee', $user->name);

        // Apply search filters
        if ($searchFilter && count($searchFilter) === 5) {
            $ordersQuery->where('generic_name', $searchFilter[0])
                ->where('brand_name', $searchFilter[1])
                ->where('form', $searchFilter[2])
                ->where('strength', $searchFilter[3])
                ->where('price', $searchFilter[4]);
        }

        // Apply status filter
        if (in_array($statusFilter, ['cancelled', 'delivered'])) {
            $ordersQuery->where('status', $statusFilter);
        }

        // Finalize query and paginate
        $orders = $ordersQuery->whereIn('status', ['cancelled', 'delivered'])
            ->orderByDesc('date_ordered')
            ->paginate($pageNum); // ✅ Pass the standard paginator to the view

        $deals = ExclusiveDeal::with('product')->where('company_id', auth('web')->user()->company_id)->get();

        return view('customer.history', [
            'orders' => $orders, // Pass the paginator object directly
            'listedDeals' => $deals,
            'current_filters' => [
                'search' => $searchFilter,
                'status' => $statusFilter,
            ],
        ]);
    }
}