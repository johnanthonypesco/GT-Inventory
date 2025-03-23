<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $userId = Auth::id();

        $totalorder = Order::where('user_id', $userId)->count();
        $pendingorder = Order::where('user_id', $userId)->where('status', 'pending')->count();
        $confirmedorder = Order::where('user_id', $userId)
            ->whereIn('status', ['completed', 'delivered'])
            ->count();
        $cancelledorder = Order::where('user_id', $userId)->where('status', 'cancelled')->count();
        $outfordelivery = Order::where('user_id', $userId)->where('status', 'delivered')->count();

        return view('customer.dashboard', [
            'totalorder' => $totalorder,
            'pendingorder' => $pendingorder,
            'confirmedorder' => $confirmedorder,
            'cancelledorder' => $cancelledorder,
            'outfordelivery' => $outfordelivery,
        ]);
    }
}
