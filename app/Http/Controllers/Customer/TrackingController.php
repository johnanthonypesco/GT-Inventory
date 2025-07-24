<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StaffLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function getStaffLocationForOrder(Order $order)
    {
        // 1. Authorize: Make sure the customer owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // 2. Check if the status is correct and a staff is assigned
        if ($order->status !== 'out for delivery' || is_null($order->staff_id)) {
            return response()->json(['error' => 'Tracking is not available for this order.'], 404);
        }

        // 3. Find the latest location for the assigned staff member
        $location = StaffLocation::where('staff_id', $order->staff_id)
                                 ->latest() // Orders by `created_at` descending
                                 ->first();

        if (!$location) {
            return response()->json(['error' => 'Location data not found for the assigned staff.'], 404);
        }

        return response()->json([
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
        ]);
    }

    
}