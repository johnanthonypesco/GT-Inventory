<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImmutableHistory;
use App\Models\Inventory;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
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

    public function updateOrder(Request $request, Order $order) {
        
        $validate = $request->validate([
            'mother_div' => 'required|string',
            'customer_id' => 'required|string', //this is the damn order ID

            'province' => 'required|string',
            'company' => 'required|string',
            'employee' => 'required|string',
            'date_ordered' => 'required|date',
            'status' => 'required|string',
            'generic_name' => 'required|string',
            'brand_name' => 'required|string',
            'form' => 'required|string',
            'quantity' => 'required|string',
            'price' => 'required|integer',
            'subtotal' => 'required|integer',
        ]);
        $validate = array_map('strip_tags', $validate);

        // TO DEDUCT THE DAMN STOCKS
        $orderDeets = Order::with(['user.company.location', 'exclusivedeal.product'])->where('id', '=', $validate['customer_id'])->first();

        if ($validate['status'] === 'delivered') {
            // params for the near expiry filter
            $today = Carbon::today();
            $threshold = Carbon::today()->addYears(3);
    
            $inventory = Inventory::where('location_id',$orderDeets->user->company->location->id)
            ->where('product_id', $orderDeets->exclusive_deal->product->id)
            ->whereBetween('expiry_date', [$today, $threshold]) // only deduct from stocks that are going to expire in a month
            ->where('quantity', '>', $validate['quantity']) // will only take from stocks that can supply the need
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();
    
            // dd($inventory);

            if (!$inventory) { // will reject the update until stocks have been found
                return back()->with("manualUpdateFailed", true);
            }

            // MIND YOU WHAT IF THE ORDER QUANTITY IS 800 BUT NO SINGULAR STOCK CAN SUPPLY THAT? THE CONDITION WILL FAIL BECAUSE IT ONLY TAKES INTO ACCOUNT ONLY ONE SINGULAR STOCK RECORD, IF THERE WAS TWO STOCK RECORDS LIKE 200 ON ONE AND THE OTHER IS 600, TECHNICALLY IT WOULD COUNT AND PASS THE IF CONDITION, BUT THE CURRENT ONE WILL FAIL BECAUSE IT ONLY DOES SINGULAR COMPARISONS. I WILL FIX THIS ONCE IT BECOMES MORE RELEVANT 
            // 
            // --SIGRAE GYAD DAMN IT

            // KUYA HAS MADE A SOLUTION IN INVENTORYCONTROLLER

            if ($inventory->quantity >= $validate['quantity']) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $validate['quantity']
                ], ['inventory_id' => $inventory->inventory_id]);

                // dd("deducted successfully");
            } else {
                dd("inventory stock not enough");
            }
        }
        // TO DEDUCT THE DAMN STOCKS

        $order->update($validate);

        $mother = $validate['mother_div']; // save our mother :)

        // ADD TO THE ORDER HISTORY ARCHIVE IF DELIVERED OR CANCELLED
        if ($validate['status'] === 'delivered' || $validate['status'] ===  "cancelled") {
            unset($validate['mother_div']);

            ImmutableHistory::createOrFirst([
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
        }

        // dd($inventory);

        return to_route('admin.order')->with("update-success", $mother);
    }
}
