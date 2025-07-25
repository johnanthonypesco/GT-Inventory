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
     * (No changes needed in this method)
     */
    public function index()
    {
        try {
            // 1. Get current stock levels
            $currentStocks = Inventory::with("product")
                ->where('expiry_date', '>=', now()->toDateString())
                ->get()
                ->groupBy(fn($stock) => optional($stock->product)->generic_name . "|" . optional($stock->product)->brand_name)
                ->map->sum('quantity');

            // 2. Get active orders
            $allActiveOrders = Order::with(['user.company.location', 'exclusive_deal.product'])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->orderBy('date_ordered', 'desc')
                ->get();

            // 3. Process orders
            $processedOrders = $allActiveOrders->map(function ($order) use ($currentStocks) {
                $productKey = optional($order->exclusive_deal->product)->generic_name . "|" . optional($order->exclusive_deal->product)->brand_name;
                $provinceKey = optional($order->user->company->location)->province ?? 'Uncategorized';
                $companyKey = optional($order->user->company)->name ?? 'Unknown Company';
                $userDateKey = (optional($order->user)->name ?? 'Unknown User') . '|' . Carbon::parse($order->date_ordered)->toDateString();

                $order->available_stock = $currentStocks->get($productKey, 0);
                $order->is_insufficient = $order->available_stock < $order->quantity;

                $order->grouping_keys = [
                    'province' => $provinceKey,
                    'company' => $companyKey,
                    'user_date' => $userDateKey,
                ];

                return $order;
            });

            // 4. Calculate Summary Counts
            $summary = [
                'ordersThisWeek' => Order::whereBetween('date_ordered', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'pendingOrders' => $processedOrders->where('status', 'pending')->count(),
                'insufficientOrders' => $processedOrders->where('is_insufficient', true)->count(),
                'insufficientProducts' => $processedOrders->where('is_insufficient', true)->unique(fn ($o) => optional($o->exclusive_deal->product)->generic_name . "|" . optional($o->exclusive_deal->product)->brand_name)->count(),
            ];

            // 5. Group orders
            $ordersByProvince = $processedOrders
                ->groupBy('grouping_keys.province')
                ->map(fn($provinces) => $provinces->groupBy('grouping_keys.company')
                    ->map(fn($companies) => $companies->groupBy('grouping_keys.user_date')
                        ->map->values()
                    )
                );

            // 6. Return response
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
     * [NEW] Get available staff for a specific order's location.
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
     * [MODIFIED] Update the status of an order, now with staff assignment and stock deduction.
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
            $originalStatus = $order->status;

            // Assign staff if status is 'out for delivery'
            if ($newStatus === 'out for delivery') {
                $order->staff_id = $validated['staff_id'];
            }

            // Handle stock deduction when order is marked as 'delivered'
            if ($newStatus === 'delivered' && $originalStatus !== 'delivered') {
                $locationId = $order->user->company->location->id;
                $productId = $order->exclusive_deal->product->id;
                $quantity = $order->quantity;

                $inventories = Inventory::where('location_id', $locationId)
                    ->where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->where('expiry_date', '>=', now()) // Only non-expired stock
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->get();

                if ($inventories->sum('quantity') < $quantity) {
                    throw new \Exception('Not enough stock available to fulfill this order.');
                }

                $quantityToDeduct = $quantity;
                foreach ($inventories as $inventory) {
                    if ($quantityToDeduct <= 0) break;
                    $deductFromThisBatch = min($inventory->quantity, $quantityToDeduct);
                    $inventory->quantity -= $deductFromThisBatch;
                    $inventory->save();
                    $quantityToDeduct -= $deductFromThisBatch;
                }
            }

            // Update the order status
            $order->status = $newStatus;
            $order->save();

            // Create an immutable history record for the change
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
                'quantity' => $order->quantity,
                'price' => $order->exclusive_deal->price,
                'subtotal' => $order->quantity * $order->exclusive_deal->price,
            ]);

            DB::commit();

            Log::info('Order status updated by mobile staff.', [
                'order_id' => $order->id, 'new_status' => $newStatus, 'staff_user_id' => Auth::id()
            ]);

            return response()->json(['message' => 'Order status updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update order status from mobile for Order ID {$order->id}: " . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }
}
