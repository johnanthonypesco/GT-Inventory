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
use Illuminate\Support\Facades\Auth;
class MobileStaffOrdersController extends Controller
{
    /**
     * Get all active orders and summary counts for the staff mobile app.
     */
    public function index()
    {
        try {
            // 1. Get current stock levels for all non-expired products.
            $currentStocks = Inventory::with("product")
                ->where('expiry_date', '>=', now()->toDateString())
                ->get()
                ->groupBy(function ($stock) {
                    return optional($stock->product)->generic_name . "|" . optional($stock->product)->brand_name;
                })
                ->map->sum('quantity');

            // 2. Eager-load all necessary relationships for active orders to prevent performance issues.
            $allActiveOrders = Order::with(['user.company.location', 'exclusive_deal.product'])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->orderBy('date_ordered', 'desc')
                ->get();

            // 3. Process each order to add necessary data before grouping.
            // This makes the logic clearer and easier to debug.
            $processedOrders = $allActiveOrders->map(function ($order) use ($currentStocks) {
                // Define keys for grouping, safely handling potentially null relationships.
                $productKey = optional($order->exclusive_deal->product)->generic_name . "|" . optional($order->exclusive_deal->product)->brand_name;
                $provinceKey = optional($order->user->company->location)->province ?? 'Uncategorized';
                $companyKey = optional($order->user->company)->name ?? 'Unknown Company';
                $userDateKey = (optional($order->user)->name ?? 'Unknown User') . '|' . Carbon::parse($order->date_ordered)->toDateString();
                
                // Add calculated properties directly to the order object.
                $order->available_stock = $currentStocks->get($productKey, 0); // Default to 0 if no stock
                $order->is_insufficient = $order->available_stock < $order->quantity;
                
                // Add grouping keys to the object for cleaner grouping later.
                $order->grouping_keys = [
                    'province' => $provinceKey,
                    'company' => $companyKey,
                    'user_date' => $userDateKey,
                ];

                return $order;
            });

            // 4. Calculate Summary Counts.
            $insufficientOrdersCount = $processedOrders->where('is_insufficient', true)->count();
            
            // To count insufficient products, we group by the product name and count the unique groups.
            $insufficientProductsCount = $processedOrders
                ->where('is_insufficient', true)
                ->unique(function ($order) {
                    return optional($order->exclusive_deal->product)->generic_name . "|" . optional($order->exclusive_deal->product)->brand_name;
                })
                ->count();
            
            $summary = [
                'ordersThisWeek' => Order::whereBetween('date_ordered', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'pendingOrders' => $processedOrders->where('status', 'pending')->count(),
                'insufficientOrders' => $insufficientOrdersCount,
                'insufficientProducts' => $insufficientProductsCount,
            ];

            // 5. Group the processed orders for the final JSON structure.
            // This is now much more readable than the previous nested structure.
            $ordersByProvince = $processedOrders
                ->groupBy('grouping_keys.province')
                ->map(fn($provinces) => $provinces->groupBy('grouping_keys.company')
                    ->map(fn($companies) => $companies->groupBy('grouping_keys.user_date')
                        // Ensure the final level is a simple array for the mobile app
                        ->map->values()
                    )
                );

            // 6. Return the final JSON response.
            return response()->json([
                'summary' => $summary,
                'ordersByProvince' => $ordersByProvince,
            ]);

        } catch (\Exception $e) {
            // Log the detailed error for debugging purposes in production.
            Log::error('MobileStaffOrdersController Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            // Return a generic error to the mobile app.
            return response()->json([
                'message' => 'An error occurred while fetching orders.',
                'error' => 'Server Error', // Don't expose detailed error messages to the client
            ], 500);
        }
    }

    /**
     * Update the status of a single order product.
     */
    public function updateProductStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'delivered', 'cancelled'])],
        ]);

        try {
            // Note: Add deduction logic here if status is 'delivered'.
            $order->status = $validated['status'];
            $order->save();

            Log::info('Single order status updated by mobile staff.', [
                'order_id' => $order->id, 'new_status' => $validated['status'], 'staff_user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Product status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to update single order status from mobile.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while updating the status.'], 500);
        }
    }
}