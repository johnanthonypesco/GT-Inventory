<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MobileStaffOrdersController extends Controller
{
    /**
     * Get all active orders and summary counts for the staff mobile app.
     * This logic is synchronized with the web version's OrderController.
     */
    public function index()
    {
        // 1. Get current stock levels, identical to the web controller's logic.
        // This handles cases where all stock for a product is expired.
        $currentStocks = Inventory::with("product")
            ->get()
            ->groupBy(function ($stock) {
                return $stock->product->generic_name . "|" . $stock->product->brand_name;
            })
            ->map(function ($productStocks) {
                $nonExpired = $productStocks->where('expiry_date', '>=', now());
                if ($nonExpired->isEmpty()) {
                    return 'expired'; // All stock for this product is expired.
                }
                return $nonExpired->sum('quantity'); // Return total quantity of non-expired stock.
            });

        // 2. Get all active orders (not delivered or cancelled).
        $allActiveOrders = Order::with(['user.company.location', 'exclusive_deal.product'])
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderBy('date_ordered', 'desc')
            ->get();

        // 3. Calculate Insufficient Orders & Products (same logic as web).
        // First, group orders by product name for easy lookup.
        $orderArray = $allActiveOrders->groupBy(function ($order) {
            if (!$order->exclusive_deal || !$order->exclusive_deal->product) {
                return 'Unknown Product';
            }
            return $order->exclusive_deal->product->generic_name . "|" . $order->exclusive_deal->product->brand_name;
        })->all();

        // Pair each order with its corresponding stock level.
        $orderStockPairs = [];
        foreach ($currentStocks as $productName => $totalStock) {
            if (isset($orderArray[$productName])) {
                foreach ($orderArray[$productName] as $order) {
                    $orderStockPairs[] = [
                        "stockInfo" => ["name" => $productName, "total" => $totalStock],
                        "orderInfo" => $order,
                    ];
                }
            }
        }

        // Filter to find only the pairs that are insufficient.
        $insufficients = collect($orderStockPairs)->filter(function ($pair) {
            $totalStock = $pair["stockInfo"]["total"];
            $quantityNeeded = $pair["orderInfo"]["quantity"];
            // An order is insufficient if all stock is expired OR if available stock is less than needed.
            return $totalStock === 'expired' || $totalStock < $quantityNeeded;
        });

        // 4. Calculate Summary Counts (same logic as web).
        $ordersThisWeek = Order::whereBetween('date_ordered', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $pendingOrders = $allActiveOrders->where('status', 'pending')->count();
        
        // Count of unique products that are insufficient.
        $insufficientProductsCount = $insufficients->groupBy('stockInfo.name')->count();
        
        // Count of total individual orders that cannot be fulfilled.
        $insufficientOrdersCount = $insufficients->count();

        // 5. Structure data for the mobile app response.
        // We add the calculated `available_stock` to each order object.
        $allActiveOrders->each(function ($order) use ($currentStocks) {
            $productKey = optional($order->exclusive_deal->product)->generic_name . "|" . optional($order->exclusive_deal->product)->brand_name;
            $order->available_stock = $currentStocks->get($productKey, 0); // Assign stock (number or 'expired')
        });

        // Group the enhanced order data for the final JSON structure.
        $ordersByProvince = $allActiveOrders
            ->groupBy(fn($order) => optional($order->user->company->location)->province ?? 'Uncategorized')
            ->map(fn($provinces) => $provinces->groupBy(fn($order) => optional($order->user->company)->name ?? 'Unknown Company')
                ->map(fn($companies) => 
                    $companies->groupBy(fn($order) => ($order->user->name ?? 'Unknown User') . '|' . Carbon::parse($order->date_ordered)->toDateString())
                              // This re-indexes the keys of each order group, ensuring it becomes a JSON array.
                              ->map->values() 
                )
            );

        // 6. Return the final JSON response.
        return response()->json([
            'summary' => [
                'ordersThisWeek' => $ordersThisWeek,
                'pendingOrders' => $pendingOrders,
                'insufficientOrders' => $insufficientOrdersCount,
                'insufficientProducts' => $insufficientProductsCount,
            ],
            'ordersByProvince' => $ordersByProvince,
        ]);
    }

    /**
     * Update the status of a single order product.
     */
    public function updateProductStatus(Request $request, \App\Models\Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'delivered', 'cancelled'])],
        ]);

        try {
            // Note: Add deduction logic here if status is 'delivered', similar to your web controller.
            // For now, this just updates the status as per your original mobile controller.
            $order->status = $validated['status'];
            $order->save();

            Log::info('Single order status updated by mobile staff.', [
                'order_id' => $order->id,
                'new_status' => $validated['status'],
                'staff_user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Product status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to update single order status from mobile.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while updating the status.'], 500);
        }
    }
}