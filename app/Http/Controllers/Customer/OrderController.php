<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrder(){
        // onlu show the company's assigned deal
        $deals = ExclusiveDeal::where('company_id', auth('web')->user()->company_id)
        ->with(['product'])
        ->get();

        // dd($deals->toArray());

        return view('customer.order', [
            'listedDeals' => $deals,
        ]);
    }
    public function storeOrder(Request $request){
        $validated = $request->validate([
            'user_id.*' => 'required|numeric',
            'exclusive_deal_id.*' => 'required|numeric',
            'quantity.*' => 'required|numeric', 
        ]);

        $validated = array_map(function ($value)  {
            return (
                is_array($value) 
                ? 
                array_map('strip_tags', $value) 
                : 
                strip_tags($value)
            );
        }, $validated);

        // dd($validated['exclusive_deal_id']);

        $checkOrders = array_map(function ($value) {
            return (
                ExclusiveDeal::where('id', $value)
                ->where('company_id', auth()->user()->company_id)->exists()
            );
        }, $validated['exclusive_deal_id']);

        $isOrderInvalid = in_array(false, $checkOrders);
        
        if (!$isOrderInvalid) {
            $orders = [];

            foreach ($validated['user_id'] as $index => $user_id) {
                $orders[] = [
                    'user_id' => $user_id,
                    'exclusive_deal_id' => $validated['exclusive_deal_id'][$index],
                    'quantity' => $validated['quantity'][$index],
                    'date_ordered' => date('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];    
            };

            Order::insert($orders);

            return to_route('customer.order')->with('success', true);
        } else {
            abort(403, 'DO NOT MODIFY THE ORDER DATA REQUEST. YOU HAVE BEEN WARNED HACKER >:(');

            // return back()->withErrors([
            //     'warning' => 'DO NOT MODIFY THE ORDER DATA REQUEST. YOU HAVE BEEN WARNED HACKER >:(',
            // ]);
        }
    }
}
