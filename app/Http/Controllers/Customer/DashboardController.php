<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ImmutableHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ExclusiveDeal; 

class DashboardController extends Controller
{
    /**
     * Display the customer's dashboard with order statistics and recent orders.
     */
    public function showDashboard()
    {
        $user = Auth::user();
        $allUserOrders = Order::where('user_id', $user->id)->get();
        $allImmutableOrders = ImmutableHistory::where('employee', $user->name)->get();

        // --- Statistics ---
        $fromNormalOrders = $allUserOrders->count();
        $fromArchivedOrders = $allImmutableOrders->count();

        $totalorder = $fromArchivedOrders + $fromNormalOrders;
        $pendingorder = $allUserOrders->where('status', 'pending')->count();
        $packedOrder = $allUserOrders->where('status', 'packed')->count();
        $outfordelivery = $allUserOrders->where('status', 'out for delivery')->count();
        $deliveredOrder = $allImmutableOrders->where('status', 'delivered')->count();
        $cancelledorder = $allImmutableOrders->where('status', 'cancelled')->count();
        
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
            'outfordelivery' => $outfordelivery,
            'packedOrder' => $packedOrder,
            'cancelledorder' => $cancelledorder,
            'deliveredOrder' => $deliveredOrder,

            'recentOrders' => $recentOrders,
            'exclusiveDeals' => $exclusiveDeals,
            'lastDeliveredOrder' => $lastDeliveredOrder,
            'lastDeliveredOrderItems' => $lastDeliveredOrderItems,
        ]);
    }
}