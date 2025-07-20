<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ScannedQrCode;
use App\Models\ImmutableHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function showOrder()
    {
        $orders = Order::with(['user.company.location', 'exclusive_deal.product']) 
        ->whereNotIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered', 'desc')
        ->get()    
        // groups orders by province
        ->groupBy(function ($orders)  { 
            return $orders->user->company->location->province;
        })
        // groups the province orders by company name
        ->map(function ($provinces) { 
            return $provinces->groupBy(function ($orders) {
                return $orders->user->company->name;
            });
        })
        // groups the company name orders by employee name & order date
        ->map(function ($provinces) { 
            return $provinces->map(function ($companies) {
                return $companies->groupBy(function ($orders) {
                    return $orders->user->name . '|' . $orders->date_ordered;
                });
            });
        })
        //  groups the employee name & order date orders by status
        ->map(function ($provinces) {
            return $provinces->map(function ($companies) {
                return $companies->map(function ($employees) {
                    return $employees->groupBy(function ($orders) {
                        return $orders->status;
                    });
                });
            });
        });

        // To get the current summary of total stocks ng bawat product na hindi pa expired
        // $currentStocks = Inventory::with("product")
        // // ->where('quantity', '>', 0) nicomment ko para masama ung stock na zero ung quantity..
        // ->whereDate('expiry_date', '>=', Carbon::today())->get()
        // ->groupBy(function ($stocks) {
        //     return $stocks->product->generic_name . "|" . $stocks->product->brand_name;
        // })
        // ->map(function ($products) {
        //     $total = $products->sum("quantity");
        //     return $total;
        // });

    $currentStocks = Inventory::with("product")
    ->get()
    ->groupBy(function ($stock) {
        return $stock->product->generic_name . "|" . $stock->product->brand_name;
    })
    ->map(function ($productStocks) {
        $nonExpired = $productStocks->where('expiry_date', '>=', now());

        if ($nonExpired->isEmpty()) {
            return 'expired';
        }

        return $nonExpired->sum('quantity');
    });
        // dd($currentStocks->toArray());
        
        // To get only the orders grouped by name
        $orderArray = Order::with(['user.company', 'exclusive_deal.product']) 
        ->whereNotIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered', 'desc')
        ->get()
        ->groupBy(function ($order) {
            if (!$order->exclusive_deal || !$order->exclusive_deal->product) {
                return 'Unknown Product';
            }
            return $order->exclusive_deal->product->generic_name. "|" . $order->exclusive_deal->product->brand_name;
        })->toArray();


        // // To connect the pair dynamic between the order quantity and the stock quantity 
        $insufficients = [];

        foreach ($currentStocks as $productName => $total) {
            if (isset($orderArray[$productName])) {
                foreach ($orderArray[$productName] as $orderz) {
                    $insufficients[] = [
                        "currentInfo" => [
                            "name" => $productName, 
                            "total" => $total
                        ],
                        "currentOrder" => $orderz,
                    ];
                }
            }
        }

        // // Groups the non-suppliable by product-name
        // $insufficients = collect($insufficients)->groupBy(function ($pair) {
        //     if ($pair["currentInfo"]["total"] < $pair["currentOrder"]["quantity"]) {
        //         return $pair["currentInfo"]["name"];
        //     } else {
        //         return "rejecteds";
        //     }
        // })->forget('rejecteds'); // removes the rejected group from the collection

        // dd($insufficients);

        $insufficients = collect($insufficients)->groupBy(function ($pair) {
            $total = $pair["currentInfo"]["total"];
            $quantity = $pair["currentOrder"]["quantity"];
        
            if ($total === 'expired' || $total < $quantity) {
                return $pair["currentInfo"]["name"];
            }
        
            return "rejecteds";
        })->forget('rejecteds');

        // For the Count Card Components
        $ordersThisWeek = Order::whereBetween('date_ordered', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
        $currentPendings = Order::where('status', 'pending')->get()->count();
        $currentInsufficientsproducts = $insufficients->count(); 
        $currentInsufficientsorders = $insufficients->flatten(1)->count();

        $currentInsufficientSummary = $insufficients->map(function ($orders, $productName) {
            $totalOrdered = collect($orders)->sum(function ($order) {
                return $order['currentOrder']['quantity'];
            });
        
            $available = $orders[0]['currentInfo']['total'] ?? 0;
        
            return [
                'product' => $productName,
                'available' => $available,
                'ordered' => $totalOrdered,
            ];
        });

        return view('admin.order', [
            'provinces' => $orders,
            'stocksAvailable' => $currentStocks->toArray(),
            'insufficients' => $insufficients,

            'ordersThisWeek' => $ordersThisWeek,
            'currentPendings' => $currentPendings,
            'insufficientproducts' => $currentInsufficientsproducts,
            'insufficientOrders' => $currentInsufficientsorders,
            'insufficientSummary' => $currentInsufficientSummary,

            'authGuard' => Auth::guard('staff')->check(),
        ]);
    }

     public function updateOrder(Request $request, Order $order) 
    {
        DB::beginTransaction();

        try {
            $validate = $request->validate([
                'mother_div' => 'required|string',
                'order_id' => 'required|integer', 
                'province' => 'required|string',
                'company' => 'required|string',
                'employee' => 'required|string',
                'date_ordered' => 'required|date',
                'status' => 'required|string',
                'generic_name' => 'required|string',
                'brand_name' => 'required|string',
                'form' => 'required|string',
                'quantity' => 'required|integer',
                'price' => 'required|numeric',
                'subtotal' => 'required|numeric',
            ]);

            $orderId = $validate['order_id'];
            $orderDeets = Order::with(['user.company.location', 'exclusive_deal.product'])->findOrFail($orderId);

            if ($validate['status'] === 'delivered') {
                $locationId = $orderDeets->user->company->location->id;
                $productId = $orderDeets->exclusive_deal->product->id;
                $quantity = $validate['quantity'];

                $inventories = Inventory::where('location_id', $locationId)
                    ->where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->get();

                if ($inventories->sum('quantity') < $quantity) {
                    throw new \Exception('Not enough stock available to fulfill this order.');
                }

                $quantityToDeduct = $quantity;
                $affectedBatches = [];
                foreach ($inventories as $inventory) {
                    if ($quantityToDeduct <= 0) break;
                    $deductFromThisBatch = min($inventory->quantity, $quantityToDeduct);
                    $inventory->quantity -= $deductFromThisBatch;
                    $inventory->save();
                    $quantityToDeduct -= $deductFromThisBatch;
                    $affectedBatches[] = [
                        'batch_number' => $inventory->batch_number,
    'expiry_date' => \Carbon\Carbon::parse($inventory->expiry_date)->toDateString(),
                        'deducted_quantity' => $deductFromThisBatch
                    ];
                }

                ScannedQrCode::create([
                    'order_id' => $orderId,
                    'product_name' => $validate['generic_name'],
                    'location' => $orderDeets->user->company->location->province,
                    'quantity' => $quantity,
                    'affected_batches' => $affectedBatches,
                    'scanned_at' => now(),
                    'signature' => null,
                ]);
            }

            $order->update(['status' => $validate['status']]);

            ImmutableHistory::create([
                'order_id' => $orderId, 
                'province' => $validate['province'],
                'company' => $validate['company'],
                'employee' => $validate['employee'],
                'date_ordered' => $validate['date_ordered'],
                'status' => $validate['status'],
                'generic_name' => $validate['generic_name'],
                'brand_name' => $validate['brand_name'],
                'form' => $validate['form'],
                'quantity' => $validate['quantity'],
                'price' => $validate['price'],
                'subtotal' => $validate['subtotal'],
            ]);

            DB::commit();
            return to_route('admin.order')->with("update-success", $validate['mother_div']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Manual order update failed for Order ID {$request->input('order_id', 'N/A')}: " . $e->getMessage());
            return back()->with("manualUpdateFailed", "Update failed: " . $e->getMessage());
        }
    }
}
