<?php
// app/Http/Controllers/mobile/MobileStaffDashboardController.php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Make sure the Order model is imported
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use Illuminate\Support\Facades\Cache;

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
        // [FIX] Get the authenticated staff and their location_id
        $staff = Auth::user();
        if (!$staff || !$staff->location_id) {
            return response()->json(['message' => 'Staff user not found or has no assigned location.'], 403);
        }
        $staffLocationId = $staff->location_id;

        // [FIX] Add 'whereHas' to filter orders by the staff's location
        $deliveredCount = Order::where('status', 'delivered')
            ->whereHas('user.company', function ($query) use ($staffLocationId) {
                $query->where('location_id', $staffLocationId);
            })
            ->count();

        $pendingCount = Order::where('status', 'pending')
            ->whereHas('user.company', function ($query) use ($staffLocationId) {
                $query->where('location_id', $staffLocationId);
            })
            ->count();

        $cancelledCount = Order::where('status', 'cancelled')
            ->whereHas('user.company', function ($query) use ($staffLocationId) {
                $query->where('location_id', $staffLocationId);
            })
            ->count();

        // [FIX] Replicate the unread message logic from your View Composer
        $messagesCount = Cache::remember('unread_messages_staff_' . $staff->id, 10, function () use ($staff) {
            return Conversation::where('is_read', false)
                ->where('receiver_type', 'staff')
                ->where('receiver_id', $staff->id)
                ->count();
        });

        // [FIX] Use the correctly calculated variables in the response
        return response()->json([
            'totalDelivered' => $deliveredCount,
            'pendingOrders' => $pendingCount,
            'cancelled' => $cancelledCount,
            'messages' => $messagesCount,
        ]);
    }
}
