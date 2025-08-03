<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ImmutableHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileOrderHistoryController extends Controller
{
    public function getOrderHistory()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 1: Get historical orders from the 'immutable_histories' table first.
        $historicalOrders = ImmutableHistory::where('employee', $user->name) // Assuming employee name matches user name
            ->get()
            ->map(function ($history) {
                // Create a structure that matches the 'Order' model's output
                return [
                    'id' => $history->order_id, // Use the original order_id
                    'date_ordered' => $history->date_ordered,
                    'status' => ucfirst($history->status),
                    'quantity' => $history->quantity,
                    'total' => $history->subtotal,
                    'exclusive_deal' => [ // Reconstruct the product details
                        'price' => $history->price,
                        'product' => [
                            'brand_name' => $history->brand_name,
                            'generic_name' => $history->generic_name,
                            'form' => $history->form,
                            'strength' => null, // Assuming strength is not in this table
                            'image_url' => null, // No image in history, frontend should handle null
                        ]
                    ],
                ];
            });

        // Step 2: Get only the IDs from the historical collection.
        $historicalOrderIds = $historicalOrders->pluck('id');

        // Step 3: Get active orders, but EXCLUDE any orders that are already in our history.
        // This is the key fix to prevent duplicate IDs.
        $activeOrders = Order::where('user_id', $user->id)
            ->whereNotIn('id', $historicalOrderIds) // ❗ KEY FIX: Exclude duplicate IDs
            ->with('exclusive_deal.product')
            ->get()
            ->map(function ($order) {
                $product = optional(optional($order->exclusive_deal)->product);
                return [
                    'id' => $order->id,
                    'date_ordered' => $order->created_at,
                    'status' => ucfirst($order->status),
                    'quantity' => $order->quantity,
                    'total' => $order->quantity * optional($order->exclusive_deal)->price,
                    'exclusive_deal' => $order->exclusive_deal ? [
                        'price' => $order->exclusive_deal->price,
                        'product' => [
                            'brand_name' => $product->brand_name,
                            'generic_name' => $product->generic_name,
                            'form' => $product->form,
                            'strength' => $product->strength,
                            'image_url' => $product->image_url,
                        ]
                    ] : null,
                ];
            });

        // Step 4: Merge the two distinct collections and sort by date.
        $allOrders = $activeOrders->merge($historicalOrders)->sortByDesc('date_ordered')->values();

        return response()->json([
            'success' => true,
            'orders' => $allOrders
        ]);
    }
    
    // The getOrderDetails method remains the same.
    public function getOrderDetails($orderId)
    {
        // ... no changes needed here ...
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $history = ImmutableHistory::where('order_id', $orderId)
            ->first();

        if ($history) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $history->order_id,
                    'date_ordered' => $history->date_ordered,
                    'status' => ucfirst($history->status),
                    'quantity' => $history->quantity,
                    'total' => $history->subtotal,
                    'exclusive_deal' => [
                        'price' => $history->price,
                        'product' => [
                            'brand_name' => $history->brand_name,
                            'generic_name' => $history->generic_name,
                            'form' => $history->form,
                            'strength' => null,
                            'image_url' => null,
                        ]
                    ],
                ]
            ]);
        }
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->with('exclusive_deal.product')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        
        $product = optional(optional($order->exclusive_deal)->product);
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'date_ordered' => $order->created_at,
                'status' => ucfirst($order->status),
                'quantity' => $order->quantity,
                'total' => $order->quantity * optional($order->exclusive_deal)->price,
                'exclusive_deal' => $order->exclusive_deal ? [
                    'price' => $order->exclusive_deal->price,
                    'product' => [
                        'brand_name' => $product->brand_name,
                        'generic_name' => $product->generic_name,
                        'form' => $product->form,
                        'strength' => $product->strength,
                        'image_url' => $product->image_url,
                    ]
                ] : null,
            ]
        ]);
    }
}