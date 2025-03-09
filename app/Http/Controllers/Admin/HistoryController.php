<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //  
    public function showHistory(){
        $orders = Order::with(['user.company', 'exclusive_deal.product'])
        ->whereIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered', 'desc')
        ->get()    
        // groups orders by company name
        ->groupBy(function ($orders)  { 
            return $orders->user->company->name;
        })
        // groups the grouped orders into employee names & the corresponding date
        ->map(function ($companies) { 
            return $companies->groupBy(function ($orders) {
                return $orders->user->name . '|' . $orders->date_ordered;
            });
        })
        // groups the grouped orders into statuses
        ->map(function ($employees) { 
            return $employees->map(function ($statuses) {
                return $statuses->groupBy(function ($orders) {
                    return $orders->status;
                });
            });
        });

        return view('admin.history', [
            "companies" => $orders,
        ]);
    }
}
