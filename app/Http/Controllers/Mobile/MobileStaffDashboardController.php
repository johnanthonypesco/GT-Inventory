<?php
// app/Http/Controllers/mobile/MobileStaffDashboardController.php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Make sure the Order model is imported

class MobileStaffDashboardController extends Controller
{
    /**
     * Get statistics for the staff dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        // Count orders for each status
        $deliveredCount = Order::where('status', 'delivered')->count();
        $pendingCount = Order::where('status', 'pending')->count(); // Assuming 'pending' is a status you use
        $cancelledCount = Order::where('status', 'cancelled')->count(); // Assuming 'cancelled' is a status you use

        // You would likely get the message count from a different model/table
        // For now, we will hardcode it as it is in your example.
        $messagesCount = 0;

        // Return the data as a JSON response
        return response()->json([
            'totalDelivered' => $deliveredCount,
            'pendingOrders' => $pendingCount,
            'cancelled' => $cancelledCount,
            'messages' => $messagesCount,
        ]);
    }
}