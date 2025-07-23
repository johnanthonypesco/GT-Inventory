<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['cancelled', 'delivered'])
            ->with('exclusive_deal.product')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                // Safely get product details
                $product = optional(optional($order->exclusive_deal)->product);

                return [
                    'id' => $order->id,
                    'date_ordered' => $order->created_at,
                    'status' => ucfirst($order->status),
                    'quantity' => $order->quantity,
                    'total' => $order->quantity * optional($order->exclusive_deal)->price,
                    // The product data is now included directly for the list view if needed,
                    // but more importantly, it's structured for the details view.
                    'exclusive_deal' => $order->exclusive_deal ? [
                        'id' => $order->exclusive_deal->id,
                        'price' => $order->exclusive_deal->price,
                        'product' => [
                            'brand_name' => $product->brand_name,
                            'generic_name' => $product->generic_name,
                            'form' => $product->form,
                            'strength' => $product->strength,
                            // ✅ Use the 'image_url' from the Product model's accessor
                            'image_url' => $product->image_url, 
                        ]
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function getOrderDetails($orderId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->with('exclusive_deal.product')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        
        // Safely get product details
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
                    'id' => $order->exclusive_deal->id,
                    'price' => $order->exclusive_deal->price,
                    'product' => [
                        'brand_name' => $product->brand_name,
                        'generic_name' => $product->generic_name,
                        'form' => $product->form,
                        'strength' => $product->strength,
                        // ✅ Use the 'image_url' from the Product model's accessor
                        'image_url' => $product->image_url,
                    ]
                ] : null,
            ]
        ]);
    }
}