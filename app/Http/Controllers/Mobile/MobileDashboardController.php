<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileDashboardController extends Controller
{
    /**
     * Fetch all data needed for the customer's mobile dashboard.
     */
    public function getDashboardData()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // --- Fetch all orders once for efficiency ---
        $allUserOrders = Order::where('user_id', $user->id)->get();

        // --- Order Statistics ---
        $stats = [
            'total' => $allUserOrders->count(),
            'pending' => $allUserOrders->whereIn('status', ['pending', 'Pending'])->count(),
            'confirmed' => $allUserOrders->whereIn('status', ['confirmed', 'Confirmed'])->count(),
            'delivered' => $allUserOrders->whereIn('status', ['delivered', 'Delivered', 'Completed', 'completed'])->count(),
            'cancelled' => $allUserOrders->whereIn('status', ['cancelled', 'Cancelled'])->count(),
        ];
        
        // --- Recent Orders (Last 5) ---
        $recentOrders = $allUserOrders
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => ucfirst($order->status),
                    'date_ordered' => $order->created_at->toIso8601String(),
                ];
            })->values();

        // --- Exclusive Deals (Last 3) ---
        $exclusiveDeals = ExclusiveDeal::where('company_id', $user->company_id)
            ->with('product') 
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($deal) {
                return [
                    'id' => $deal->id,
                    'price' => $deal->price,
                    'product' => [
                        'brand_name' => optional($deal->product)->brand_name,
                        'generic_name' => optional($deal->product)->generic_name,
                        'image_url' => optional($deal->product)->image_url,
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => $user->name,
                ],
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'exclusiveDeals' => $exclusiveDeals,
            ]
        ]);
    }
}