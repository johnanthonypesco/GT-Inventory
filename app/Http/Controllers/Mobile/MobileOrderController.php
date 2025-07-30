<?php

namespace App\Http\Controllers\Mobile;

use Log;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ExclusiveDeal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MobileOrderController extends Controller
{
    // ... (storeOrder and index methods are unchanged) ...
    public function storeOrder(Request $request)
    {
        \Log::info("📌 Request Data:", $request->all());
    
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Please log in again.'], 401);
        }
    
        \Log::info("✅ Authenticated User:", ['user_id' => $user->id]);
    
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
    
            $orders[] = [
                'user_id' => $user->id,
                'exclusive_deal_id' => $item['exclusive_deal_id'],
                'quantity' => $item['quantity'],
                'date_ordered' => now()->toDateString(), 
                'status' => 'pending',
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
            ], 401);
        }
    
        $deals = ExclusiveDeal::where('company_id', $user->company_id)
            ->with(['product' => function ($query) {
                $query->select('id', 'brand_name', 'generic_name', 'form', 'strength', 'img_file_path');
            }])
            ->get();

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
            // ✅ UPDATED: Added 'out for delivery' to the query
            ->whereIn('status', ['pending', 'packed', 'out for delivery']) 
            ->with(['exclusive_deal.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            $exclusiveDeal = $order->exclusive_deal;
            $product = $exclusiveDeal->product ?? null;

            return [
                'orderId' => $order->id,
                'created_at' => $order->created_at->toIso8601String(),
                'totalAmount' => '₱' . number_format($order->quantity * ($exclusiveDeal->price ?? 0), 2),
                'status' => ucfirst(str_replace('-', ' ', $order->status)), // Formats 'out-for-delivery' to 'Out for delivery'
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