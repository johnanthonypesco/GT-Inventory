<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    /**
     * Display the customer's dashboard with order statistics and recent orders.
     */
    public function showDashboard()
    {
        $userId = Auth::id();

        // --- OPTIMIZED APPROACH: Fetch all orders once, then filter ---
        $allUserOrders = Order::where('user_id', $userId)->get();

        // Calculate statistics from the collection
        $totalorder = $allUserOrders->count();
        $pendingorder = $allUserOrders->where('status', 'Pending')->count();
        $confirmedorder = $allUserOrders->where('status', 'Confirmed')->count(); // Corrected logic
        $outfordelivery = $allUserOrders->where('status', 'Out for Delivery')->count(); // Corrected logic
        $completedorder = $allUserOrders->where('status', 'Completed')->count();
        $cancelledorder = $allUserOrders->where('status', 'Cancelled')->count();

        // --- NEW: Fetch the 5 most recent orders for the table ---
        $recentOrders = Order::where('user_id', $userId)
                             ->latest() // Orders by the newest first
                             ->take(5)  // Limit to 5 results
                             ->get();

        // Pass all the data to the view
        return view('customer.dashboard', [
            'totalorder' => $totalorder,
            'pendingorder' => $pendingorder,
            'confirmedorder' => $confirmedorder,
            'outfordelivery' => $outfordelivery,
            'completedorder' => $completedorder,
            'cancelledorder' => $cancelledorder,
            'recentOrders' => $recentOrders, // Pass the new variable
        ]);
    }
}