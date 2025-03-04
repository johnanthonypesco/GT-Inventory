<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //
    public function showHistory(){
        $orders = Order::where('user_id', '=', auth('web')->id())
        ->whereIn('status', ['cancelled', 'delivered'])
        ->with('exclusive_deal.product')
        ->orderBy('date_ordered', 'desc')->get()
        ->groupBy(function ($order) {
            return $order->date_ordered;
        })
        ->map(function ($groupedOrders) {
            return $groupedOrders->groupBy(function ($order) {
                return $order->status;
            });
        });

        // dd($orders->toArray());

        return view('customer.history', [
            'groupedOrdersByDate' => $orders,
        ]);
    }
}
