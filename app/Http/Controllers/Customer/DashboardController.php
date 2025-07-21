<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ExclusiveDeal; 

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        $allUserOrders = Order::where('user_id', $user->id)->get();

        // --- Statistics ---
        $totalorder = $allUserOrders->count();
        $pendingorder = $allUserOrders->where('status', 'Pending')->count();
        $confirmedorder = $allUserOrders->where('status', 'Confirmed')->count();
        $outfordelivery = $allUserOrders->where('status', 'Out for Delivery')->count();
        $completedorder = $allUserOrders->where('status', 'Completed')->count();
        $cancelledorder = $allUserOrders->where('status', 'Cancelled')->count();
        
        // --- Data for Dashboard Widgets ---
        $recentOrders = Order::where('user_id', $user->id)
                             ->latest()
                             ->take(10)
                             ->get();

        // The ->where('is_active', true) filter has been removed from this query.
        $exclusiveDeals = ExclusiveDeal::where('company_id', $user->company_id)
                                ->latest()
                                ->take(3)
                                ->get();
        

        $lastDeliveredOrder = $allUserOrders
->where('status', 'delivered')
->sortByDesc('created_at')
->first();

$lastDeliveredOrderItems = collect();

if ($lastDeliveredOrder) {
$lastDeliveredOrderItems = Order::with('exclusive_deal.product')
->where('user_id', $user->id)
->where('created_at', $lastDeliveredOrder->created_at)
 ->get();
}


        return view('customer.dashboard', [
            'totalorder' => $totalorder,
            'pendingorder' => $pendingorder,
            'confirmedorder' => $confirmedorder,
            'outfordelivery' => $outfordelivery,
            'completedorder' => $completedorder,
            'cancelledorder' => $cancelledorder,
            'recentOrders' => $recentOrders,
            'exclusiveDeals' => $exclusiveDeals,
            'lastDeliveredOrder' => $lastDeliveredOrder,
            'lastDeliveredOrderItems' => $lastDeliveredOrderItems,
        ]);
    }
}