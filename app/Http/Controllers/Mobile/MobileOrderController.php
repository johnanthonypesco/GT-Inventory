<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ExclusiveDeal; // Assuming this is the products table

class MobileOrderController extends Controller
{
    public function index()
    {
        $products = ExclusiveDeal::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_price' => 0,
        ]);

        $totalPrice = 0;

        foreach ($validatedData['orders'] as $item) {
            $product = ExclusiveDeal::find($item['id']);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $item['quantity'] * $product->price,
            ]);

            $totalPrice += $orderItem->price;
        }

        $order->update(['total_price' => $totalPrice]);

        return response()->json(['success' => true, 'message' => 'Order placed successfully.']);
    }
}