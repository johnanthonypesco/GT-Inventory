<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Conversation;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\ScannedQrCode;
use App\Models\ExclusiveDeal;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    View::composer('*', function ($view) {
        // Get the current user
        $currentUser = Auth::user();

        // Initialize variables
        $unreadMessagesAdmin = 0;
        $unreadMessagesSuperAdmin = 0;
        $unreadMessagesStaff = 0;
        $adminsidebar_counter = 0;

        // Check who is logged in
        if ($currentUser instanceof SuperAdmin) {
            $unreadMessagesSuperAdmin = Cache::remember('unread_messages_superadmin', 60, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'super_admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesSuperAdmin;
        } elseif ($currentUser instanceof Admin) {
            $unreadMessagesAdmin = Cache::remember('unread_messages_admin', 60, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesAdmin;
        } elseif ($currentUser instanceof Staff) {
            $unreadMessagesStaff = Cache::remember('unread_messages_staff', 60, function () use ($currentUser) {
                return Conversation::where('is_read', false)
                    ->where('receiver_type', 'staff')
                    ->where('receiver_id', $currentUser->id)
                    ->count();
            });
            $adminsidebar_counter = $unreadMessagesStaff;
        }

        // Get total orders count
        $totalOrders = Cache::remember('total_orders', 60, function () {
            return Order::count();
        });

        // Get pending orders count
        $pendingOrders = Cache::remember('pending_orders', 60, function () {
            return Order::where('status', 'pending')->count();
        });

        // Get cancelled orders count
        $cancelledOrders = Cache::remember('cancelled_orders', 60, function () {
            return Order::where('status', 'cancelled')->count();
        });

        // Get most sold products for the charts
        $mostSoldProducts = Cache::remember('most_sold_products', 60, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
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
        $lowSoldProducts = Cache::remember('low_sold_products', 60, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
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
        $moderateSoldProducts = Cache::remember('moderate_sold_products', 60, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
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
        $inventoryLevels = Cache::remember('inventory_levels', 60, function () {
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

        // Get deducted quantities for the inventory chart
        $deductedQuantities = Cache::remember('deducted_quantities', 60, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->where('orders.status', 'delivered') // Only delivered orders
                ->select(
                    DB::raw('MONTH(orders.created_at) as month'),
                    DB::raw('SUM(orders.quantity) as total_deducted')
                )
                ->groupBy(DB::raw('MONTH(orders.created_at)'))
                ->orderBy('month', 'ASC')
                ->get();
        });

        // Get low stock alerts (products with quantity <= 50)
        $lowStockProducts = Cache::remember('low_stock_products', 60, function () {
            return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->having('total_quantity', '<=', 50)
                ->get();
        });

        // Get out-of-stock products (products with quantity = 0)
        $outOfStockProducts = Cache::remember('out_of_stock_products', 60, function () {
            return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->having('total_quantity', '=', 0)
                ->get();
        });

        // Get order fulfillment rate
        $orderFulfillmentRate = Cache::remember('order_fulfillment_rate', 60, function () use ($totalOrders) {
            $fulfilledOrders = Order::where('status', 'completed')->count();
            return $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;
        });

        // Get inventory data grouped by month
        $inventoryByMonth = Cache::remember('inventory_by_month', 60, function () {
            return Inventory::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month', 'ASC')
            ->get();
        });

        // Get available years for filtering
        $availableYears = Cache::remember('available_years', 60, function () {
            return Inventory::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'DESC')
                ->pluck('year');
        });

        // Fetch revenue data (only completed orders)
        $revenueData = Cache::remember('revenue_data', 60, function () {
            return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->where('orders.status', 'delivered') // Only completed orders
                ->select(
                    DB::raw('DATE(orders.created_at) as date'),
                    DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue')
                )
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();
        });

        $totalRevenue = $revenueData->sum('total_revenue');
        // Prepare labels and data for the revenue line graph
        $revenueLabels = $revenueData->pluck('date');
        $revenueValues = $revenueData->pluck('total_revenue');

        // Share the data with the view
        $view->with([
            'unreadMessagesAdmin' => $unreadMessagesAdmin,
            'unreadMessagesSuperAdmin' => $unreadMessagesSuperAdmin,
            'unreadMessagesStaff' => $unreadMessagesStaff,
            'adminsidebar_counter' => $adminsidebar_counter,
            'currentUser' => $currentUser,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'cancelledOrders' => $cancelledOrders,
            'labels' => $labels,
            'data' => $data,
            'lowSoldLabels' => $lowSoldLabels,
            'lowSoldData' => $lowSoldData,
            'moderateSoldLabels' => $moderateSoldLabels,
            'moderateSoldData' => $moderateSoldData,
            'inventoryLabels' => $inventoryLabels,
            'inventoryData' => $inventoryData,
            'deductedQuantities' => $deductedQuantities,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'orderFulfillmentRate' => $orderFulfillmentRate,
            'inventoryByMonth' => $inventoryByMonth,
            'availableYears' => $availableYears,
            'revenueLabels' => $revenueLabels,
            'revenueValues' => $revenueValues,
            'totalRevenue' => $totalRevenue,
        ]);
    });
}
}