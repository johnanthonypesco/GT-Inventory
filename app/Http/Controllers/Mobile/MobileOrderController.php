<?php

namespace App\Http\Controllers\Mobile;

use Log;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ExclusiveDeal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MobileOrderController extends Controller
{
    public function storeOrder(Request $request)
    {
        \Log::info("📌 Request Data:", $request->all());
    
        // ✅ Use authenticated user instead of relying on `user_id` in request
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Please log in again.'], 401);
        }
    
        \Log::info("✅ Authenticated User:", ['user_id' => $user->id]);
    
        // ✅ Validate request (No need to pass `user_id` in the request)
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.exclusive_deal_id' => 'required|exists:exclusive_deals,id',
            'orders.*.quantity' => 'required|integer|min:1',
        ]);
    
        $orders = [];
        foreach ($validated['orders'] as $item) {
            $deal = ExclusiveDeal::where('id', $item['exclusive_deal_id'])
                ->where('company_id', $user->company_id)
                ->first();
    
            if (!$deal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order data. Attempt detected.'
                ], 403);
            }
    
            // $orders[] = [
            //     'user_id' => $user->id, // ✅ Automatically use authenticated user ID
            //     'exclusive_deal_id' => $item['exclusive_deal_id'],
            //     'quantity' => $item['quantity'],
            //     // 'date' => now(),
            //     'status' => 'pending',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ];

            $orders[] = [
                'user_id' => $user->id,
                'exclusive_deal_id' => $item['exclusive_deal_id'],
                'quantity' => $item['quantity'],
                'date_ordered' => date('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];    
        
        }
    
        try {
            Order::insert($orders);
            return response()->json(['success' => true, 'message' => 'Order placed successfully.']);
        } catch (\Exception $e) {
            \Log::error("❌ Order Insert Error:", ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Database error. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    

    public function index()
    {
        $user = Auth::user();
    
        if (!$user) {
            \Log::error('Unauthorized access. Headers received:', request()->headers->all());
    
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. No user authenticated.',
                'debug' => request()->headers->all(), // Show request headers
            ], 401);
        }
    
         $deals = ExclusiveDeal::where('company_id', $user->company_id)
        ->with(['product' => function ($query) {
            $query->select('id', 'brand_name', 'generic_name', 'form', 'strength', 'img_file_path');
        }])
        ->get();

    // ✅ Ensure image paths are absolute
    foreach ($deals as $deal) {
        if ($deal->product->img_file_path) {
            $deal->product->img_file_path = asset('image/download.jpg');
        } else {
            $deal->product->img_file_path = asset('image/download.jpg'); // Default image
        }
    }
    return response()->json($deals);



}


public function getUserOrders()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Please log in again.',
        ], 401);
    }

    \Log::info("✅ Fetching orders for user:", ['user_id' => $user->id]);

    $orders = Order::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'completed', 'partial-delivery'])
        ->with(['exclusive_deal.product']) // ✅ Corrected relationship name
        ->orderBy('date_ordered', 'desc')
        ->get();

    // ✅ Handle potential null values
    $formattedOrders = $orders->map(function ($order) {
        $exclusiveDeal = $order->exclusive_deal;
        $product = $exclusiveDeal->product ?? null; // ✅ Avoids null reference errors

        return [
            'orderId' => $order->id,
            'dateOrdered' => $order->date_ordered, // ✅ Ensure consistency
            'totalAmount' => '₱' . number_format($order->quantity * ($exclusiveDeal->price ?? 0), 2),
            'status' => ucfirst($order->status),
            'items' => [
                [
                    'brand' => $product->brand_name ?? 'Unknown',
                    'generic' => $product->generic_name ?? 'Unknown',
                    'form' => $product->form ?? 'Unknown',
                    'strength' => $product->strength ?? 'Unknown',
                    'quantity' => $order->quantity,
                    'total' => '₱' . number_format($order->quantity * ($exclusiveDeal->price ?? 0), 2),
                ]
            ]
        ];
    });

    return response()->json([
        'success' => true,
        'orders' => $formattedOrders
    ]);
}
}
