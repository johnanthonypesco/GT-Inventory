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
            ->orderBy('date_ordered', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'date_ordered' => $order->date_ordered,
                    'status' => $order->status,
                    'quantity' => $order->quantity,
                    'total' => $order->quantity * optional($order->exclusive_deal)->price, // Prevent errors if null
                    'exclusive_deal' => $order->exclusive_deal ? [
                        'id' => $order->exclusive_deal->id,
                        'price' => $order->exclusive_deal->price,
                        'product' => $order->exclusive_deal->product ? [
                            'brand_name' => $order->exclusive_deal->product->brand_name,
                            'form' => $order->exclusive_deal->product->form,
                            'strength' => $order->exclusive_deal->product->strength,
                        ] : null
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

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'date_ordered' => $order->date_ordered,
                'status' => $order->status,
                'quantity' => $order->quantity,
                'total' => $order->quantity * optional($order->exclusive_deal)->price,
                'exclusive_deal' => optional($order->exclusive_deal) ? [
                    'id' => $order->exclusive_deal->id,
                    'price' => $order->exclusive_deal->price,
                    'product' => optional($order->exclusive_deal->product) ? [
                        'brand_name' => $order->exclusive_deal->product->brand_name,
                        'form' => $order->exclusive_deal->product->form,
                        'strength' => $order->exclusive_deal->product->strength,
                    ] : null
                ] : null,
            ]
        ]);
    }
}
