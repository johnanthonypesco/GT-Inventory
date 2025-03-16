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
        // Share data with all views
        View::composer('*', function ($view) {
            // Kunin ang current user
            $currentUser = Auth::user();

            // I-initialize ang mga variables
            $unreadMessagesAdmin = 0;
            $unreadMessagesSuperAdmin = 0;
            $unreadMessagesStaff = 0;
            $adminsidebar_counter = 0;

            // I-check kung sino ang naka-login
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

            // Kunin ang total orders count
            $totalOrders = Cache::remember('total_orders', 60, function () {
                return Order::count();
            });

            // Kunin ang pending orders count
            $pendingOrders = Cache::remember('pending_orders', 60, function () {
                return Order::where('status', 'pending')->count();
            });

            // Kunin ang cancelled orders count
            $cancelledOrders = Cache::remember('cancelled_orders', 60, function () {
                return Order::where('status', 'cancelled')->count();
            });

            // Kunin ang most sold products for the charts
            $mostSoldProducts = Cache::remember('most_sold_products', 60, function () {
                return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                    ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->orderBy('total_quantity', 'DESC')
                    ->limit(6)
                    ->get();
            });

            // Kunin ang labels at data para sa most sold products chart
            $labels = [];
            $data = [];
            foreach ($mostSoldProducts as $product) {
                $labels[] = $product->generic_name;
                $data[] = $product->total_quantity;
            }

            // Kunin ang low sold products (products with quantity <= 10)
            $lowSoldProducts = Cache::remember('low_sold_products', 60, function () {
                return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                    ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->having('total_quantity', '<=', 10)
                    ->get();
            });

            // Kunin ang labels at data para sa low sold products chart
            $lowSoldLabels = [];
            $lowSoldData = [];
            foreach ($lowSoldProducts as $product) {
                $lowSoldLabels[] = $product->generic_name;
                $lowSoldData[] = $product->total_quantity;
            }

            // Kunin ang moderate sold products (products with quantity > 10 and <= 50)
            $moderateSoldProducts = Cache::remember('moderate_sold_products', 60, function () {
                return Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                    ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->having('total_quantity', '>', 10)
                    ->having('total_quantity', '<=', 50)
                    ->get();
            });

            // Kunin ang labels at data para sa moderate sold products chart
            $moderateSoldLabels = [];
            $moderateSoldData = [];
            foreach ($moderateSoldProducts as $product) {
                $moderateSoldLabels[] = $product->generic_name;
                $moderateSoldData[] = $product->total_quantity;
            }

            // Kunin ang inventory levels for the inventory chart
            $inventoryLevels = Cache::remember('inventory_levels', 60, function () {
                return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->orderBy('total_quantity', 'ASC')
                    ->limit(6)
                    ->get();
            });

            // Kunin ang labels at data para sa inventory chart
            $inventoryLabels = [];
            $inventoryData = [];
            foreach ($inventoryLevels as $inventory) {
                $inventoryLabels[] = $inventory->generic_name;
                $inventoryData[] = $inventory->total_quantity;
            }

            // Kunin ang low stock alerts (products with quantity <= 50)
            $lowStockProducts = Cache::remember('low_stock_products', 60, function () {
                return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->having('total_quantity', '<=', 50)
                    ->get();
            });

            // Kunin ang out-of-stock products (products with quantity = 0)
            $outOfStockProducts = Cache::remember('out_of_stock_products', 60, function () {
                return Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                    ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                    ->groupBy('products.generic_name')
                    ->having('total_quantity', '=', 0)
                    ->get();
            });

            // Kunin ang order fulfillment rate
            $orderFulfillmentRate = Cache::remember('order_fulfillment_rate', 60, function () use ($totalOrders) {
                $fulfilledOrders = Order::where('status', 'completed')->count();
                return $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;
            });

            // I-share ang data sa view
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
                'lowStockProducts' => $lowStockProducts,
                'outOfStockProducts' => $outOfStockProducts,
                'orderFulfillmentRate' => $orderFulfillmentRate,
            ]);
        });
    }
}