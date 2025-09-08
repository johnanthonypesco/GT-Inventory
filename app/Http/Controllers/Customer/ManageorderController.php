<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Order;
use Illuminate\Http\Request;

class ManageorderController extends Controller
{
    //
    public function showManageOrder(Request $request){
        $searchFilter = $request->input('search_filter', null);
        $searchFilter = str_replace(['₱', ','], '', $searchFilter);
        $searchFilter = $searchFilter ? explode(' - ', $searchFilter) : null;
        
        $statusFilter = $request->input('status_filter', 'all');

        $pageNum = 10;
        
        $orders = Order::with('purchase_order')->where('user_id', auth('web')->id());

        if ($searchFilter && count($searchFilter) === 5) {
            $orders = $orders->whereHas('exclusive_deal.product', function ($query) use ($searchFilter) {
                $query->where('generic_name', $searchFilter[0])
                ->where('brand_name', $searchFilter[1])
                ->where('form', $searchFilter[2])
                ->where('strength', $searchFilter[3]);
            });

            $orders = $orders->whereHas('exclusive_deal', function ($query) use ($searchFilter) {
                $query->where('price', $searchFilter[4]);
            });
        }

        if (in_array($statusFilter, ['pending', 'packed', 'out for delivery'])) {
            $orders = $orders->where('status', $statusFilter);
        }

        $orders = $orders->whereIn('status', ['pending', 'packed', 'out for delivery'])
        ->with('exclusive_deal.product')
        ->orderBy('date_ordered', 'desc')
        ->paginate($pageNum);

        $grouped = $orders->getCollection()
            ->groupBy(fn($o) => $o->date_ordered)
            ->map(fn($d) => $d->groupBy('status'));

        $orders->setCollection(collect($grouped));

        $deals = ExclusiveDeal::with('product')->where('company_id', auth('web')->user()->company_id)->get();

        return view('customer.manageorder', [
            'groupedOrdersByDate' => $orders,
            'listedDeals' => $deals,

            'current_filters' => [
                'search' => $searchFilter,
                'status' => $statusFilter,
            ],
        ]);
    }
}
