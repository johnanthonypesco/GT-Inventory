<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\ImmutableHistory;
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
     * This logic is now aligned with the web Admin/OrderController to ensure consistent counts.
     */
    public function index()
    {
        try {
            // ✅ Step 1: Get current stock levels, grouped by product AND location.
            // This is the key change to match the web controller's logic.
            $currentStocks = Inventory::with(["product", "location"])
                ->get()
                ->groupBy(function ($stock) {
                    $product = $stock->product;
                    $location = $stock->location;
                    // Ensure stock has valid product and location to prevent errors
                    if (!$product || !$location) {
                        return 'invalid_stock_record';
                    }
                    // Create a unique key: Generic|Brand|Form|Strength|Province
                    return "{$product->generic_name}|{$product->brand_name}|{$product->form}|{$product->strength}|{$location->province}";
                })
                ->map(function ($productStocks) {
                    // Check for non-expired stock within the group
                    $nonExpired = $productStocks->where('expiry_date', '>=', now());
                    // If all stock for this product/location is expired, mark it as 'expired'
                    if ($nonExpired->isEmpty()) {
                        return 'expired';
                    }
                    // Otherwise, return the sum of available quantity
                    return $nonExpired->sum('quantity');
                });

            // Step 2: Get all active (non-delivered, non-cancelled) orders
            $allActiveOrders = Order::with(['user.company.location', 'exclusive_deal.product'])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->orderBy('date_ordered', 'desc')
                ->get();

            // Step 3: Process each order to check its stock availability
            $processedOrders = $allActiveOrders->map(function ($order) use ($currentStocks) {
                $product = optional($order->exclusive_deal)->product;
                $location = optional($order->user->company)->location;

                // Ensure order has necessary details
                if (!$product || !$location) {
                    $order->available_stock = 0;
                    $order->is_insufficient = true;
                    return $order;
                }

                // ✅ Create the same location-specific key for the order to look up its stock
                $productKey = "{$product->generic_name}|{$product->brand_name}|{$product->form}|{$product->strength}|{$location->province}";
                
                // Get the available stock using the specific key
                $order->available_stock = $currentStocks->get($productKey, 0);

                // Determine if the order is insufficient
                $order->is_insufficient = ($order->available_stock === 'expired' || 
                                          (is_numeric($order->available_stock) && $order->available_stock < $order->quantity));

                // Add grouping keys for easier structuring on the frontend
                $order->grouping_keys = [
                    'province' => $location->province,
                    'company' => optional($order->user->company)->name ?? 'Unknown Company',
                    'user_date' => (optional($order->user)->name ?? 'Unknown User') . '|' . Carbon::parse($order->date_ordered)->toDateString(),
                ];

                return $order;
            });

            // Step 4: Calculate summary counts based on the processed orders
            $insufficientOrders = $processedOrders->filter(fn($order) => $order->is_insufficient);

            // Count of individual order lines that cannot be fulfilled
            $insufficientOrderLinesCount = $insufficientOrders->count();

            // Group by product to count unique insufficient products
            $insufficientProductGroups = $insufficientOrders->groupBy(function($order) {
                $product = optional($order->exclusive_deal)->product;
                return "{$product->generic_name}|{$product->brand_name}|{$product->form}|{$product->strength}|{$order->grouping_keys['province']}";
            });

            // Count of unique products that are insufficient
            $insufficientProductsCount = $insufficientProductGroups->count();

            // Create detailed summaries for the modal views
            $insufficientSummary = $insufficientProductGroups->map(function($orders, $productKey) {
                return [
                    'product' => $productKey,
                    'available' => $orders->first()->available_stock,
                    'ordered' => $orders->sum('quantity'),
                ];
            });

            $insufficientOrderLines = $insufficientOrders->map(function ($order) {
                return [
                    'date_ordered' => Carbon::parse($order->date_ordered)->toIso8601String(),
                    'company' => optional($order->user->company)->name ?? 'Unknown',
                    'employee' => optional($order->user)->name ?? 'Unknown User',
                    'generic_name' => optional($order->exclusive_deal->product)->generic_name ?? 'N/A',
                    'brand_name' => optional($order->exclusive_deal->product)->brand_name ?? 'N/A',
                    'available' => $order->available_stock,
                    'ordered' => $order->quantity,
                ];
            })->values();

            // Step 5: Calculate total orders this week (same logic as web)
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            $normalOrdersThisWeek = Order::whereIn('status', ['pending', 'packed', 'out for delivery'])
                ->whereBetween('date_ordered', [$startOfWeek, $endOfWeek])->count();
            
            $archivedOrdersThisWeek = ImmutableHistory::whereIn('status', ['cancelled', 'delivered'])
                ->whereBetween('date_ordered', [$startOfWeek, $endOfWeek])->count();

            // Final summary object for the API response
            $summary = [
                'ordersThisWeek' => $archivedOrdersThisWeek + $normalOrdersThisWeek,
                'pendingOrders' => $allActiveOrders->where('status', 'pending')->count(),
                'insufficientOrders' => $insufficientOrderLinesCount,
                'insufficientProducts' => $insufficientProductsCount,
                'insufficientSummary' => $insufficientSummary->values()->all(),
                'insufficientOrderLines' => $insufficientOrderLines,
            ];

            // Step 6: Group orders for display on the mobile screen
            $ordersByProvince = $processedOrders
                ->groupBy('grouping_keys.province')
                ->map(fn($provinces) => $provinces->groupBy('grouping_keys.company')
                    ->map(fn($companies) => $companies->groupBy('grouping_keys.user_date')
                        ->map(fn($userDateGroup) => $userDateGroup->values())
                    )
                );

            return response()->json([
                'summary' => $summary,
                'ordersByProvince' => $ordersByProvince,
            ]);
            
        } catch (\Exception $e) {
            Log::error('MobileStaffOrdersController Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'An error occurred while fetching orders.', 'error' => 'Server Error'], 500);
        }
    }

    /**
     * Get available staff for a specific order's location.
     */
    public function getAvailableStaff(Order $order)
    {
        try {
            $customerLocationId = $order->user->company->location_id;

            if (!$customerLocationId) {
                return response()->json(['message' => 'Customer location not found.'], 404);
            }

            $staff = Staff::where('location_id', $customerLocationId)
                ->whereNull('archived_at') // Only active staff
                ->get(['id', 'staff_username', 'email']);

            return response()->json($staff);
        } catch (\Exception $e) {
            Log::error('Failed to get available staff for mobile: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve staff list.'], 500);
        }
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'packed', 'out for delivery', 'delivered', 'cancelled'])],
            'staff_id' => 'nullable|integer|required_if:status,out for delivery|exists:staff,id'
        ]);

        DB::beginTransaction();
        try {
            $newStatus = $validated['status'];
            
            if ($newStatus === 'out for delivery') {
                $order->staff_id = $validated['staff_id'];
            }

            // The logic for stock deduction is now primarily handled by the QR scan process.
            // This update is for status changes only. If a 'delivered' status is forced here,
            // it assumes stock was handled elsewhere or isn't required for this action.
            
            $order->status = $newStatus;
            $order->save();

            // Create an immutable history record for the change
            if (in_array($newStatus, ['delivered', 'cancelled'])) {
                 ImmutableHistory::create([
                    'order_id' => $order->id,
                    'province' => $order->user->company->location->province,
                    'company' => $order->user->company->name,
                    'employee' => $order->user->name,
                    'date_ordered' => $order->date_ordered,
                    'status' => $newStatus,
                    'generic_name' => $order->exclusive_deal->product->generic_name,
                    'brand_name' => $order->exclusive_deal->product->brand_name,
                    'form' => $order->exclusive_deal->product->form,
                    'strength' => $order->exclusive_deal->product->strength,
                    'quantity' => $order->quantity,
                    'price' => $order->exclusive_deal->price,
                    'subtotal' => $order->quantity * $order->exclusive_deal->price,
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Order status updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update order status from mobile for Order ID {$order->id}: " . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }
}
