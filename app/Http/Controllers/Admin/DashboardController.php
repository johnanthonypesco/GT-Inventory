<?php

namespace App\Http\Controllers\Admin;

use DB;
use Cache;
use Auth;
use App\Models\Order;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\Conversation;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\ScannedQrCode;
use App\Models\ExclusiveDeal;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $locations = Location::all();
        
        // Get the current user
        $currentUser = Auth::user();

        // Initialize variables
        $unreadMessagesAdmin = 0;
        $unreadMessagesSuperAdmin = 0;
        $unreadMessagesStaff = 0;
        $adminsidebar_counter = 0;

        // Check who is logged in
        if ($currentUser instanceof SuperAdmin) {
            $unreadMessagesSuperAdmin = Cache::remember('unread_messages_superadmin', 10, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'super_admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesSuperAdmin;
        } elseif ($currentUser instanceof Admin) {
            $unreadMessagesAdmin = Cache::remember('unread_messages_admin', 10, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesAdmin;
        } elseif ($currentUser instanceof Staff) {
            $unreadMessagesStaff = Cache::remember('unread_messages_staff', 10, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'staff')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesStaff;
        }

        // Get total orders count (only delivered orders)
        $totalOrders = Cache::remember('total_orders', 10, function () {
            return Order::where('status', 'delivered')->count();
        });

        // Get pending orders count
        $pendingOrders = Cache::remember('pending_orders', 10, function () {
            return Order::where('status', 'pending')->count();
        });

        // Get cancelled orders count
        $cancelledOrders = Cache::remember('cancelled_orders', 10, function () {
            return Order::where('status', 'cancelled')->count();
        });

        // Get most sold products for the charts
        $mostSoldProducts = Cache::remember('most_sold_products', 10, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                ->where('orders.status', 'delivered') // Only count delivered orders for sales data
                ->groupBy('products.generic_name')
                ->orderBy('total_quantity', 'DESC')
                ->limit(6)
                ->get();
        });

        // Get labels and data for most sold products chart
        $labels = [];
        $data = [];
        foreach ($mostSoldProducts as $product) {
            $labels[] = $product->generic_name;
            $data[] = $product->total_quantity;
        }

        // Get low sold products (products with quantity <= 10)
        $lowSoldProducts = Cache::remember('low_sold_products', 10, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                ->where('orders.status', 'delivered') // Only count delivered orders
                ->groupBy('products.generic_name')
                ->having('total_quantity', '<=', 10)
                ->get();
        });

        // Get labels and data for low sold products chart
        $lowSoldLabels = [];
        $lowSoldData = [];
        foreach ($lowSoldProducts as $product) {
            $lowSoldLabels[] = $product->generic_name;
            $lowSoldData[] = $product->total_quantity;
        }

        // Get moderate sold products (products with quantity > 10 and <= 50)
        $moderateSoldProducts = Cache::remember('moderate_sold_products', 10, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                ->where('orders.status', 'delivered') // Only count delivered orders
                ->groupBy('products.generic_name')
                ->having('total_quantity', '>', 10)
                ->having('total_quantity', '<=', 50)
                ->get();
        });

        // Get labels and data for moderate sold products chart
        $moderateSoldLabels = [];
        $moderateSoldData = [];
        foreach ($moderateSoldProducts as $product) {
            $moderateSoldLabels[] = $product->generic_name;
            $moderateSoldData[] = $product->total_quantity;
        }

        // Get inventory levels for the inventory chart
        $inventoryLevels = Cache::remember('inventory_levels', 10, function () {
            return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->orderBy('total_quantity', 'ASC')
                ->limit(6)
                ->get();
        });

        // Get labels and data for inventory chart
        $inventoryLabels = [];
        $inventoryData = [];
        foreach ($inventoryLevels as $inventory) {
            $inventoryLabels[] = $inventory->generic_name;
            $inventoryData[] = $inventory->total_quantity;
        }

        // Get deducted quantities for delivered orders by generic_name
        $deductedQuantities = Cache::remember('deducted_quantities_by_generic_name', 10, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->where('orders.status', 'delivered')
                ->select(
                    'products.generic_name',
                    DB::raw('SUM(orders.quantity) as total_deducted')
                )
                ->groupBy('products.generic_name')
                ->orderBy('total_deducted', 'DESC')
                ->get();
        });

        $deductedLabels = $deductedQuantities->pluck('generic_name');
        $deductedData = $deductedQuantities->pluck('total_deducted');

        // Get low stock alerts (products with quantity <= 50)
        $lowStockProducts = Cache::remember('low_stock_products', 10, function () {
            return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->having('total_quantity', '<=', 50)
                ->get();
        });

        // Get out-of-stock products (products with quantity = 0)
        $outOfStockProducts = Cache::remember('out_of_stock_products', 10, function () {
            return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->having('total_quantity', '=', 0)
                ->get();
        });

        // Get order fulfillment rate (using delivered orders as fulfilled)
        $orderFulfillmentRate = Cache::remember('order_fulfillment_rate', 10, function () use ($totalOrders) {
            $fulfilledOrders = Order::where('status', 'delivered')->count();
            return $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;
        });

        // Get inventory data grouped by month
        $inventoryByMonth = Cache::remember('inventory_by_month', 10, function () {
            return Inventory::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month', 'ASC')
            ->get();
        });

        // Get available years for filtering
        $availableYears = Cache::remember('available_years', 10, function () {
            return Inventory::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'DESC')
                ->pluck('year');
        });

        // Revenue data (only from delivered orders)
        $revenueData = Cache::remember('revenue_data', 10, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->where('orders.status', 'delivered')
                ->select(
                    DB::raw('DATE(orders.date_ordered) as date'),
                    DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                )
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();
        });

        $totalRevenue = $revenueData->sum('total_revenue');
        $revenueLabels = $revenueData->pluck('date');
        $revenueValues = $revenueData->pluck('total_revenue');

        return view('admin.dashboard', compact(
            'locations',
            'unreadMessagesAdmin',
            'unreadMessagesSuperAdmin',
            'unreadMessagesStaff',
            'adminsidebar_counter',
            'currentUser',
            'totalOrders', // This now only counts delivered orders
            'pendingOrders',
            'cancelledOrders',
            'labels',
            'data',
            'lowSoldLabels',
            'lowSoldData',
            'moderateSoldLabels',
            'moderateSoldData',
            'inventoryLabels',
            'inventoryData',
            'deductedQuantities',
            'lowStockProducts',
            'outOfStockProducts',
            'orderFulfillmentRate',
            'inventoryByMonth',
            'availableYears',
            'revenueLabels',
            'revenueValues',
            'totalRevenue',
            'deductedLabels',
            'deductedData'
        ));
    }

    // ... (rest of the controller methods remain the same)
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

    public function getFilteredDeductedQuantities($year, $month, $locationId = null)
{
    $deductedQuery = Order::with(['exclusiveDeal.product', 'exclusiveDeal.inventories'])
        ->where('status', 'delivered')
        ->whereYear('updated_at', $year)
        ->whereMonth('updated_at', $month);

    if ($locationId) {
        $deductedQuery->whereHas('exclusiveDeal.inventories', function($q) use ($locationId) {
            $q->where('location_id', $locationId);
        });
    }

    $deductedQuantities = $deductedQuery->get()
        ->groupBy('exclusiveDeal.product.generic_name')
        ->map(function($orders) {
            return $orders->sum('quantity');
        })
        ->sortDesc()
        ->take(10);

    return response()->json([
        'labels' => $deductedQuantities->keys(),
        'deductedData' => $deductedQuantities->values()
    ]);
}

    public function getRevenueData($period, $year, $month = null, $week = null)
{
    // Validate inputs
    if (!in_array($period, ['day', 'week', 'month', 'year'])) {
        return response()->json(['error' => 'Invalid period'], 400);
    }

    $query = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
        ->where('orders.status', 'delivered')
        ->whereYear('orders.date_ordered', $year);

    // Apply period-specific filters and groupings
    switch ($period) {
        case 'day':
            if (!$month) return response()->json(['error' => 'Month required for daily data'], 400);
            
            $query->whereMonth('orders.date_ordered', $month)
                  ->select(
                      DB::raw('DAY(orders.date_ordered) as period_value'),
                      DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                  )
                  ->groupBy('period_value')
                  ->orderBy('period_value');
            break;

        case 'week':
            if (!$month) return response()->json(['error' => 'Month required for weekly data'], 400);
            
            $query->whereMonth('orders.date_ordered', $month)
                  ->select(
                      DB::raw('WEEK(orders.date_ordered, 1) - WEEK(DATE_FORMAT(orders.date_ordered, "%Y-%m-01"), 1) + 1 as period_value'),
                      DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                  )
                  ->groupBy('period_value')
                  ->orderBy('period_value');
            break;

        case 'month':
            $query->select(
                      DB::raw('MONTH(orders.date_ordered) as period_value'),
                      DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                  )
                  ->groupBy('period_value')
                  ->orderBy('period_value');
            break;

        case 'year':
            $query->select(
                      DB::raw('YEAR(orders.date_ordered) as period_value'),
                      DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                  )
                  ->groupBy('period_value')
                  ->orderBy('period_value');
            break;
    }

    $data = $query->get()->keyBy('period_value');

    // Generate complete dataset with all possible periods
    $labels = [];
    $values = [];
    $valueMap = $data->pluck('total_revenue', 'period_value')->toArray();

    switch ($period) {
        case 'day':
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $labels[] = date('M j', mktime(0, 0, 0, $month, $day, $year));
                $values[] = $valueMap[$day] ?? 0;
            }
            break;

        case 'week':
            $date = new \DateTime("$year-$month-01");
            $weeksInMonth = ceil(($date->format('t') + $date->format('N')) / 7);
            
            for ($weekNum = 1; $weekNum <= $weeksInMonth; $weekNum++) {
                $labels[] = "Week $weekNum";
                $values[] = $valueMap[$weekNum] ?? 0;
            }
            break;

        case 'month':
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
                $labels[] = $monthNames[$monthNum - 1];
                $values[] = $valueMap[$monthNum] ?? 0;
            }
            break;

        case 'year':
            // Get all available years from the database
            $availableYears = Order::select(DB::raw('YEAR(date_ordered) as year'))
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->toArray();
            
            foreach ($availableYears as $availYear) {
                $labels[] = $availYear;
                $values[] = $valueMap[$availYear] ?? 0;
            }
            break;
    }

    return response()->json([
        'labels' => $labels,
        'values' => $values,
        'total' => array_sum($values),
        'average' => count($values) > 0 ? array_sum($values) / count($values) : 0
    ]);
}
}