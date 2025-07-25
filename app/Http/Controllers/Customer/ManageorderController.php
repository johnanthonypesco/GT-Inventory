<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ManageorderController extends Controller
{
    //
    public function showManageOrder(){
        $orders = Order::where('user_id', '=', auth('web')->id())
        ->whereIn('status', ['pending', 'packed', 'out for delivery'])
        ->with('exclusive_deal.product')
        ->orderBy('date_ordered', 'desc')->get()
        ->groupBy(function ($orders) {
            return $orders->date_ordered;
        })
        ->map(function ($dates) {
            return $dates->groupBy(function ($orders) {
                return $orders->status;
            });
        });
        
        // dd($orders->toArray());

        return view('customer.manageorder', [
            'groupedOrdersByDate' => $orders,
        ]);
    }
}
