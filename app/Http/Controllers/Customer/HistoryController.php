<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\ImmutableHistory;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //
    public function showHistory(Request $request){
        $searchFilter = $request->input('search_filter', null);
        $searchFilter = str_replace(['₱', ','], '', $searchFilter);
        $searchFilter = $searchFilter ? explode(' - ', $searchFilter) : null;
        
        $statusFilter = $request->input('status_filter', 'all');

        $pageNum = 10;
        
        $user = auth('web')->user();

        $orders = ImmutableHistory::where('user_id', $user->id);

        // Apply search filters if present and valid
        if ($searchFilter && count($searchFilter) === 5) {
            $orders = $orders->where('generic_name', $searchFilter[0])
            ->where('brand_name', $searchFilter[1])
            ->where('form', $searchFilter[2])
            ->where('strength', $searchFilter[3])
            ->where('price', $searchFilter[4]);
        }

        // Apply status filtering if valid
        if (in_array($statusFilter, ['cancelled', 'delivered'])) {
            $orders = $orders->where('status', $statusFilter);
        }

        // Para siguradong we're only paginating valid statuses
        $orders = $orders->whereIn('status', ['cancelled', 'delivered'])
        ->orderByDesc('date_ordered')
        ->paginate($pageNum);

        // Group by date, and then by status
        $grouped = $orders->getCollection()
        ->groupBy(fn($o) => $o->date_ordered)
        ->map(fn($d) => $d->groupBy('status'));

        $orders->setCollection(collect($grouped));

        $deals = ExclusiveDeal::with('product')->where('company_id', auth('web')->user()->company_id)->get();

        return view('customer.history', [
            'groupedOrdersByDate' => $orders,
            'listedDeals' => $deals,

            'current_filters' => [
                'search' => $searchFilter,
                'status' => $statusFilter,
            ],
        ]);
    }
}
