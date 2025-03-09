<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //  
    public function showHistory(){
        $orders = Order::with(['user.company.location', 'exclusive_deal.product']) // NEED TO ALSO GROUP THIS BY LOCATION
        ->whereIn('status', ['delivered', 'cancelled'])
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

        return view('admin.history', [
            "provinces" => $orders,
        ]);
    }
}
