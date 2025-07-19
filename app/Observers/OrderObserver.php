<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Ito ay tatakbo sa tuwing may bagong order na ginawa.
     */
    public function created(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Order "updated" event.
     * Ito ay tatakbo kung may na-update sa isang order (hal. status from pending to delivered).
     */
    public function updated(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Order "deleted" event.
     * Ito ay tatakbo kung may order na na-delete.
     */
    public function deleted(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Isang helper method para hindi na ulit-ulitin ang code.
     * Dito natin ilalagay ang lahat ng cache keys na gusto nating alisin.
     */
    protected function clearDashboardCache(): void
    {
        // Gamitin ang parehong keys na ginamit mo sa DashboardController
        Cache::forget('dashboard.total_orders');
        Cache::forget('dashboard.pending_orders');
        Cache::forget('dashboard.cancelled_orders');
        Cache::forget('dashboard.total_revenue');
        Cache::forget('dashboard.most_sold_products');
        Cache::forget('dashboard.low_sold_products');
        Cache::forget('dashboard.moderate_sold_products');
        Cache::forget('dashboard.low_stock_products');
    }
}