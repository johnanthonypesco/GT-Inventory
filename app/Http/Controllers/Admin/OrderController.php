<?php

namespace App\Http\Controllers\Admin;

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
        $currentStocks = Inventory::with("product")
        ->where('quantity', '>', 0)
        ->whereDate('expiry_date', '>=', Carbon::today())->get()
        ->groupBy(function ($stocks) {
            return $stocks->product->generic_name . "|" . $stocks->product->brand_name;
        })
        ->map(function ($products) {
            $total = $products->sum("quantity");
            return $total;
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
            return $order->exclusive_deal->product->generic_name . "|" . $order->exclusive_deal->product->brand_name;
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
        $insufficients = collect($insufficients)->groupBy(function ($pair) {
            if ($pair["currentInfo"]["total"] < $pair["currentOrder"]["quantity"]) {
                return $pair["currentInfo"]["name"];
            } else {
                return "rejecteds";
            }
        })->forget('rejecteds'); // removes the rejected group from the collection

        // dd($insufficients);

        // For the Count Card Components
        $ordersThisWeek = Order::whereBetween('date_ordered', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
        $currentPendings = Order::where('status', 'pending')->get()->count();
        $currentInsufficients = count($insufficients);

        return view('admin.order', [
            'provinces' => $orders,
            'stocksAvailable' => $currentStocks->toArray(),
            'insufficients' => $insufficients,

            'ordersThisWeek' => $ordersThisWeek,
            'currentPendings' => $currentPendings,
            'insufficientTotal' => $currentInsufficients,

            'authGuard' => Auth::guard('staff')->check(),
        ]);
    }

    public function updateOrder(Request $request, Order $order) {
        $validate = $request->validate([
            'status' => 'required|string',
            'mother_div' => 'required|string',
        ]);

        $validate = array_map('strip_tags', $validate);

        // dd($validate['mother_div']);

        $order->update($validate);

        return to_route('admin.order')->with("update-success", $validate['mother_div']);
    }
}
