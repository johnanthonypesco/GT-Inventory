<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\SuperAdmin;
use App\Mail\OrderNotificationMail;
use Illuminate\Http\Request;
use App\Models\ExclusiveDeal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

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
            $orderDetails = [];
    
            foreach ($validated['user_id'] as $index => $user_id) {
                $user = User::findOrFail($user_id);
                $deal = ExclusiveDeal::with('product')->findOrFail($validated['exclusive_deal_id'][$index]);
                $locationId = $user->company->location_id;
    
                $availableQty = Inventory::where('location_id', $locationId)
                    ->where('product_id', $deal->product_id)
                    ->sum('quantity');
    
                // $availableQty = $inventory ? $inventory->quantity : 0;
                $isAvailable = $availableQty >= $validated['quantity'][$index];
    
                // Save for email
                $orderDetails[] = [
                    'user' => $user->name,
                    'product' => $deal->product->generic_name,
                    'quantity_requested' => $validated['quantity'][$index],
                    'available' => $isAvailable,
                    'available_quantity' => $availableQty,
                    'location' => $user->company->location->province . ', ' . $user->company->location->city,
                ];
    
                $orders[] = [
                    'user_id' => $user_id,
                    'exclusive_deal_id' => $validated['exclusive_deal_id'][$index],
                    'quantity' => $validated['quantity'][$index],
                    'date_ordered' => date('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            Order::insert($orders);
    
            // Notify admins


                $admins = Admin::all();
                $superadmins = SuperAdmin::all();

                foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new OrderNotificationMail($orderDetails));
                }

                foreach ($superadmins as $superadmin) {
                Mail::to($superadmin->email)->send(new OrderNotificationMail($orderDetails));
                }
                

            
    
            return to_route('customer.order')->with('success', true);
        } else {
            abort(403, 'DO NOT MODIFY THE ORDER DATA REQUEST. YOU HAVE BEEN WARNED HACKER >:(');
        }
    }
}