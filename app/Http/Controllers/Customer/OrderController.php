<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Models\ExclusiveDeal;
use App\Mail\OrderNotificationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    public function showOrder(Request $request){
        $searchFilter = $request->input('search_filter', null);
        $searchFilter = str_replace(['₱', ','], '', $searchFilter);
        $searchFilter = $searchFilter ? explode(' - ', $searchFilter) : null;

        // onlu show the company's assigned deal
        $deals = ExclusiveDeal::where('is_archived', false)
        ->where('company_id', auth('web')->user()->company_id)->with('product');

        if ($searchFilter && count($searchFilter) === 5) {
            $deals = $deals->whereHas('product', function ($query) use ($searchFilter) {
                $query->where('generic_name', $searchFilter[0])
                ->where('brand_name', $searchFilter[1])
                ->where('form', $searchFilter[2])
                ->where('strength', $searchFilter[3]);
            });
            

            $deals = $deals->where('price', $searchFilter[4]);
        } else {
            $deals = $deals->with('product');
        }
        
        $deals = $deals->get();

        return view('customer.order', [
            'listedDeals' => $deals,

            'current_filters' => [
                'search' => $searchFilter,
            ],
        ]);
    }

    public function storeOrder(Request $request){
        $validated = $request->validate([
            'user_id' => 'required|numeric',
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

        // OG count
        $ids = $validated['exclusive_deal_id'];

        // New count after checking if not doctored by user
        $validIds = ExclusiveDeal::whereIn('id', $ids)
            ->where('company_id', auth()->user()->company_id)
            ->pluck('id')
            ->toArray();

        // Check na ngayon if walang doctored na deal ID, na hindi belong sa company nayun.
        // if pantay lang yung counts, it means na walang na filter out na doctored IDS outside
        // sa company nayun. Which means valid yung order and wont get rejected
        // by: Optimizing Sigrae
        $isOrdersNotDoctored = count($ids) === count($validIds);
        
        if ($isOrdersNotDoctored) {
            // SIGRAE'S ARRAY
            $orders = [];

            // KUYA'S ARRAY
            $orderDetails = [];
    
            $user = User::findOrFail($validated["user_id"]);
            $locationId = $user->company->location_id;

            $deals = ExclusiveDeal::with('product')
            ->whereIn('id', $validated['exclusive_deal_id'])
            ->get()
            ->keyBy("id"); // para hindi na need ulitin query, accessin nalang gamit yung key

            $inventory = Inventory::where('location_id', $locationId)
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->pluck('total_qty', 'product_id');

            foreach ($validated['exclusive_deal_id'] as $index => $deal_id) {
                // START OF KUYA'S CODE
                $prod_id = $deals[$deal_id]->product->id;
                $availableQty = $inventory[$prod_id] ?? 0;
                
                // $availableQty = $inventory ? $inventory->quantity : 0;
                $isAvailable = $availableQty >= $validated['quantity'][$index];
                
                // Save for email
                $orderDetails[] = [
                    'user' => $user->name,
                    // 'product' => $deals[$deal_id]->product->generic_name,
                    'brand_name' =>$deals[$deal_id]->product->brand_name,
                    'generic_name' => $deals[$deal_id]->product->generic_name,
                    'form' => $deals[$deal_id]->product->form, // e.g., 'Tablet', 'Syrup'
                    'strength' => $deals[$deal_id]->product->strength, // e.g., '500mg', '250mg/5ml'
                    'quantity_requested' => $validated['quantity'][$index],
                    'available' => $isAvailable,
                    'available_quantity' => $availableQty,
                    'location' => $user->company->location->province . ', ' . $user->company->location->city,
                ];
                // END OF KUYA'S CODE

    
                // START OF SIGRAE'S CODE
                $orders[] = [
                    'user_id' => $validated['user_id'],
                    'exclusive_deal_id' => $deal_id,
                    'quantity' => $validated['quantity'][$index],
                    'date_ordered' => date('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            Order::insert($orders);
            // END OF SIGRAE'S CODE
    
            // // Notify admins
            // $admins = Admin::all();
            // $superadmins = SuperAdmin::all();

            // foreach ($admins as $admin) {
            // Mail::to($admin->email)->send(new OrderNotificationMail($orderDetails));
            // }

            // foreach ($superadmins as $superadmin) {
            // Mail::to($superadmin->email)->send(new OrderNotificationMail($orderDetails));
            // }
                OrderPlaced::dispatch($orderDetails);

    
            return to_route('customer.order')->with('success', 'Order placed successfully.');
        } else {
            abort(403, 'DO NOT MODIFY THE ORDER DATA REQUEST. YOU HAVE BEEN WARNED HACKER >:(');
        }
    }

     public function reorderLastPurchase()
    {
        $user = Auth::user();

        // 1. Find the timestamp of the user's most recent completed purchase.
        $lastOrderDate = Order::where('user_id', $user->id)
                          ->where('status', 'delivered') // <-- Change this line
                          ->latest('created_at')
                          ->value('created_at');
        // 2. If no completed order exists, redirect back.
        if (!$lastOrderDate) {
            return redirect()->back()->with('error', 'No previous completed order found to re-order.');
        }

        // 3. Get all items that were part of that last purchase.
        $lastOrderItems = Order::where('user_id', $user->id)
                               ->where('created_at', $lastOrderDate)
                               ->get();

        $newOrdersPayload = [];
        $orderDetailsForEmail = [];
        $inventoryIssues = [];

        // 4. Loop through each item from the last order to check inventory, just like in storeOrder().
        foreach ($lastOrderItems as $item) {
            $deal = ExclusiveDeal::with('product')->find($item->exclusive_deal_id);
            if (!$deal) continue; // Skip if the deal no longer exists

            $availableQty = Inventory::where('location_id', $user->company->location_id)
                                     ->where('product_id', $deal->product_id)
                                     ->sum('quantity');

            // Check if there is enough stock
            if ($availableQty < $item->quantity) {
                $inventoryIssues[] = $deal->product->generic_name;
                continue; // Skip this item but check the others
            }
            
            // If stock is available, prepare the new order line item for insertion
            $newOrdersPayload[] = [
                'user_id' => $user->id,
                'exclusive_deal_id' => $item->exclusive_deal_id,
                'quantity' => $item->quantity,
                'status' => 'pending', // All re-orders start as Pending
                'date_ordered' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Prepare details for the notification email
            $orderDetailsForEmail[] = [
                'user' => $user->name,
                'product' => $deal->product->generic_name,
                'brand_name' =>$deal->product->brand_name,
        'generic_name' => $deal->product->generic_name,
        'form' => $deal->product->form, // e.g., 'Tablet', 'Syrup'
        'strength' => $deal->product->strength, // e.g., '500mg', '250mg/5ml'
                'quantity_requested' => $item->quantity,
                'available' => true,
                'available_quantity' => $availableQty,
                'location' => $user->company->location->province . ', ' . $user->company->location->city,
            ];
        }

        // // 5. Check for any inventory issues.
        // if (!empty($inventoryIssues)) {
        //     $products = implode(', ', $inventoryIssues);
        //     return redirect()->back()->with('error', "Could not re-order. The following products are out of stock: $products.");
        // }
        
        // If there are no items to re-order (e.g., all deals were deleted), stop.
        // if (empty($newOrdersPayload)) {
        //     return redirect()->back()->with('error', 'The items from your last order are no longer available.');
        // }

        // 6. Insert the new order records into the database.
        Order::insert($newOrdersPayload);

        // 7. Send email notifications to admins, just like in storeOrder().
        // $admins = Admin::all();
        // $superadmins = SuperAdmin::all();

        // foreach ($admins as $admin) {
        //     Mail::to($admin->email)->send(new OrderNotificationMail($orderDetailsForEmail));
        // }
        // foreach ($superadmins as $superadmin) {
        //     Mail::to($superadmin->email)->send(new OrderNotificationMail($orderDetailsForEmail));
        // }
    OrderPlaced::dispatch($orderDetailsForEmail);

        // 8. Redirect with a success message.
        return redirect()->route('customer.dashboard')->with('success', 'Successfully re-ordered your last purchase.');
    }
}