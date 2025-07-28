<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\ImmutableHistory;
use App\Models\Company;
use App\Models\Conversation;

class MobileStaffDashboardController extends Controller
{
    /**
     * Get statistics for the staff dashboard from multiple tables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        $staff = Auth::user();

        if (!$staff || !$staff->location_id) {
            return response()->json(['message' => 'Unauthorized: Staff user not found or has no assigned location.'], 403);
        }

        // --- Get counts from the Order table ---
        // For pending, packed, and out for delivery orders
        $pendingCount = Order::where('status', 'pending')->count();
        $packedCount = Order::where('status', 'packed')->count();
        $outForDeliveryCount = Order::where('status', 'out for delivery')->count();

        // --- Get counts from the ImmutableHistory table ---
        // For delivered and cancelled orders
        $deliveredCount = ImmutableHistory::where('status', 'delivered')->count();
        $cancelledCount = ImmutableHistory::where('status', 'cancelled')->count();

        // Get unread message count
        $messagesCount = Cache::remember('unread_messages_staff_' . $staff->id, now()->addMinutes(5), function () use ($staff) {
            return Conversation::where('receiver_id', $staff->id)
                ->where('receiver_type', 'staff')
                ->where('is_read', false)
                ->count();
        });

        // Return the compiled statistics
        return response()->json([
            'deliveredOrders' => $deliveredCount,
            'pendingOrders' => $pendingCount,
            'packedOrders' => $packedCount,
            'outForDeliveryOrders' => $outForDeliveryCount,
            'cancelledOrders' => $cancelledCount,
            'messages' => $messagesCount,
        ]);
    }

    /**
     * Get a paginated list of orders/history filtered by a specific status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrdersByStatus(Request $request, $status)
    {
        // Validate the incoming status parameter
        $request->merge(['status' => $status]);
        $request->validate([
            'status' => ['required', Rule::in(['delivered', 'pending', 'packed', 'out for delivery', 'cancelled'])],
        ]);

        $staff = Auth::user();

        if (!$staff || !$staff->location_id) {
            return response()->json(['message' => 'Unauthorized: Staff user not found or has no assigned location.'], 403);
        }

        $results = null;

        // Conditionally query based on the status
        if (in_array($status, ['pending', 'packed', 'out for delivery'])) {
            // For order statuses, get ALL orders regardless of location
            $results = Order::with(['user:id,name'])
                ->where('status', $status)
                ->latest()
                ->paginate(15);
        } elseif (in_array($status, ['delivered', 'cancelled'])) {
            // For history statuses, get ALL from immutable_histories
            $results = ImmutableHistory::where('status', $status)
                ->latest('date_ordered')
                ->select('id', 'order_id', 'employee', 'date_ordered', 'status', 'brand_name') 
                ->paginate(15);
        }

        return response()->json($results);
    }
}