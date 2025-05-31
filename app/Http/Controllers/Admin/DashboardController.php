<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Order;
use App\Models\Location;
use App\Models\Inventory;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $locations = Location::all();
        return view('admin.dashboard', compact('locations'));
    }
    public function getInventoryByMonth($year, $month, $locationId = null)
{
    // Base query for inventory data
    $inventoryQuery = Inventory::whereYear('inventories.created_at', $year)
        ->whereMonth('inventories.created_at', $month)
        ->join('products', 'inventories.product_id', '=', 'products.id');

    // Apply location filter if provided
    if ($locationId) {
        $inventoryQuery->where('inventories.location_id', $locationId);
    }

    // Get inventory data
    $inventoryData = $inventoryQuery
        ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
        ->groupBy('products.generic_name')
        ->get();

    // Base query for deducted quantities
    $deductedQuery = Order::whereYear('orders.created_at', $year)
        ->whereMonth('orders.created_at', $month)
        ->where('orders.status', 'delivered')
        ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
        ->join('products', 'exclusive_deals.product_id', '=', 'products.id');

    // Get deducted quantities (removed location filter for orders)
    $deductedData = $deductedQuery
        ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_deducted'))
        ->groupBy('products.generic_name')
        ->get();

    return response()->json([
        'labels' => $inventoryData->pluck('generic_name'),
        'inventoryData' => $inventoryData->pluck('total_quantity'),
        'deductedData' => $deductedData->pluck('total_deducted'),
    ]);
}

public function getFilteredDeductedQuantities($year, $month)
{
    // Query to get deducted quantities (removed location filter)
    $deductedQuantities = DB::table('orders')
        ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
        ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
        ->where('orders.status', 'delivered')
        ->whereYear('orders.updated_at', $year)
        ->whereMonth('orders.updated_at', $month)
        ->select(
            'products.generic_name',
            DB::raw('SUM(orders.quantity) as total_deducted')
        )
        ->groupBy('products.generic_name')
        ->orderBy('total_deducted', 'DESC')
        ->limit(10)
        ->get();

    return response()->json([
        'labels' => $deductedQuantities->pluck('generic_name'),
        'deductedData' => $deductedQuantities->pluck('total_deducted')
    ]);
}
}
