<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrder()
    {
        $orders = Order::with(['user.company.location', 'exclusive_deal.product']) // NEED TO ALSO GROUP THIS BY LOCATION
        ->whereNotIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered', 'desc')
        ->get()    
        // groups orders by province
        ->groupBy(function ($orders)  { 
            return $orders->user->company->location->province;
        })
        // groups the province orders by company name
        ->map(function ($provinces) { 
            return $provinces->groupBy(function ($orders) {
                return $orders->user->company->name;
            });
        })
        // groups the company name orders by employee name & order date
        ->map(function ($provinces) { 
            return $provinces->map(function ($companies) {
                return $companies->groupBy(function ($orders) {
                    return $orders->user->name . '|' . $orders->date_ordered;
                });
            });
        })
        //  groups the employee name & order date orders by status
        ->map(function ($provinces) {
            return $provinces->map(function ($companies) {
                return $companies->map(function ($employees) {
                    return $employees->groupBy(function ($orders) {
                        return $orders->status;
                    });
                });
            });
        });

        // dd($orders->toArray());

        $ordersThisWeek = Order::whereBetween('date_ordered', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
        $currentPendings = Order::where('status', 'pending')->get()->count();
        $currentPartials = Order::where('status', 'partial-delivery')->get()->count();
        
        // dd($orders->toArray());

        return view('admin.order', [
            'provinces' => $orders,
            'ordersThisWeek' => $ordersThisWeek,
            'currentPendings' => $currentPendings,
            'currentPartials' => $currentPartials,
        ]);
    }

    public function updateOrder(Request $request, Order $order) {
        $validate = $request->validate([
            'status' => 'required|string',
        ]);

        $validate = array_map('strip_tags', $validate);

        $order->update($validate);

        return to_route('admin.order');
    }
}
