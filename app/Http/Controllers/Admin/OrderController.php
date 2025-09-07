<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExclusiveDeal;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ScannedQrCode;
use App\Models\ImmutableHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Historylogs;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Staff; 

class OrderController extends Controller
{
    public function showOrder(Request $request)
    {
        $employeeSearch = $request->input('employee_search', null);
        
        $orders = Order::with([
            'user.company.location',
            'exclusive_deal.product',
        ]);
        
        // If may search then add another gyad damn condition
        if ($employeeSearch !== null) {
            $employeeSearch = explode(' - ', $employeeSearch);

            $orders = $orders->whereHas('user', function ($query) use ($employeeSearch) {
                $query->where('name', $employeeSearch[0])
                    ->whereHas('company', function ($q) use ($employeeSearch) {
                        $q->where('name', $employeeSearch[1]);
                    });
            });
        }

        $orders = $orders->whereNotIn('status', ['delivered','cancelled'])
        ->orderBy('date_ordered','desc')
        ->get();

        // Explanation ng Hierchy: Province>Company>(we’ll slice per-company here)>employee+date>status>order
        $provinces = $orders
        // group muna mga orders into provinces
        ->groupBy(fn($o) => $o->user->company->location->province)
        // nilagay ko yung $request para ma kuha current URL params para hindi mwala yung current page states
        // ng bawat table
        ->map(function($provinceOrders) use ($request) {
            $perPage = 10;

            // Dito na ngayon mag grogroup ng fucking paginated companies
            return $provinceOrders->groupBy(fn($o) => $o->user->company->name)
                ->mapWithKeys(function($companyOrders, $companyName) use ($request, $perPage) {
                    $slug = Str::slug($companyName, '_');
                    $pageParam = "page_{$slug}";
                    $current = $request->input($pageParam, 1);

                    $slice = $companyOrders->forPage($current, $perPage);

                    // nag manual paginate nalang ako, masyado limited built-in ni Laravel na paginate().
                    // Now panong gumagana bawat part neto? Ewan, siguradong makakalimutan koto 
                    // in a few weeks kaya i-ChatGPT mo nalang explanation.
                    // -- by: MOTHER BUCKIN' SIGRAE
                    $paginator = new LengthAwarePaginator(
                        $slice->values(), 
                        $companyOrders->count(),
                        $perPage,
                        $current,
                        [
                            'pageName' => $pageParam,
                            'path'     => LengthAwarePaginator::resolveCurrentPath(),
                            'query'    => Arr::except($request->query(), $pageParam),
                        ]
                    );

                    // dito na ginogroup yung companies by employeeID+date & grouped by statuses
                    $grouped = $slice
                        ->groupBy(fn($o) => $o->user->id . '|' . $o->user->name .'|'.$o->date_ordered)
                        ->map(fn($empOrders) => $empOrders->groupBy(fn($o) => $o->status));

                    $grouped->paginator = $paginator;

                    return [$companyName => $grouped];
                });
        });
// dd($provinces->toArray());

    $currentStocks = Inventory::with(["product", "location"])
    ->get()
    ->groupBy(function ($stock) {
        return $stock->product->generic_name . "|" . $stock->product->brand_name . "|" . $stock->product->form . "|" . $stock->product->strength . "|" . $stock->location->province;
    })
    ->map(function ($productStocks) {
        $nonExpired = $productStocks->where('expiry_date', '>=', now());

        if ($nonExpired->isEmpty()) {
            return 'expired';
        }

        return $nonExpired->sum('quantity');
    });
    // dd($currentStocks);
        // To get only the orders grouped by name
        $orderArray = Order::with(['user.company.location', 'exclusive_deal.product']) 
        ->whereNotIn('status', ['delivered', 'cancelled'])
        ->orderBy('date_ordered', 'desc')
        ->get()
        ->groupBy(function ($order) {
            if (!$order->exclusive_deal || !$order->exclusive_deal->product) {
                return 'Unknown Product';
            }
            return $order->exclusive_deal->product->generic_name. "|" . $order->exclusive_deal->product->brand_name . "|" . $order->exclusive_deal->product->form . "|" . $order->exclusive_deal->product->strength . "|" . $order->user->company->location->province;
        })->toArray();

        // // To connect the pair dynamic between the order quantity and the stock quantity 
       $insufficients = [];

        // Start by looping through all the orders that were grouped by product.
        foreach ($orderArray as $productName => $orders) {
            // For each product, get its stock. Default to 0 if no stock record exists.
            $totalStock = $currentStocks[$productName] ?? 0;

            // Now, check each individual order for that product.
            foreach ($orders as $order) {
                $insufficients[] = [
                    "currentInfo" => [
                        "name" => $productName,
                        "total" => $totalStock // Use the determined stock level
                    ],
                    "currentOrder" => $order,
                ];
            }
        }

        $insufficients = collect($insufficients)->groupBy(function ($pair) {
            $total = $pair["currentInfo"]["total"];
            $quantity = $pair["currentOrder"]["quantity"];
        
            if ($total === 'expired' || $total < $quantity) {
                return $pair["currentInfo"]["name"];
            }
        
            return "rejecteds";
        })->forget('rejecteds');

        // For the Count Card Components
        $normalOrdersThisWeek = Order::whereIn('status', ['pending', 'packed', 'out for delivery'])
        ->whereBetween('date_ordered', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
        $archivedOrdersThisWeek = ImmutableHistory::whereIn('status', ['cancelled', 'delivered'])
        ->whereBetween('date_ordered', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
        $ordersThisWeek = $archivedOrdersThisWeek + $normalOrdersThisWeek;

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
            'provinces' => $provinces,
            'stocksAvailable' => $currentStocks->toArray(),
            'insufficients' => $insufficients,

            'ordersThisWeek' => $ordersThisWeek,
            'currentPendings' => $currentPendings,
            'insufficientproducts' => $currentInsufficientsproducts,
            'insufficientOrders' => $currentInsufficientsorders,
            'insufficientSummary' => $currentInsufficientSummary,

            'customersSearchSuggestions' => User::with('company')->get(),
            'current_search' => [
                'query' => $employeeSearch,
            ],

            'authGuard' => Auth::guard('staff')->check(),

            // for manual order creations
            'usersByCompany' => User::with('company')->get()->groupBy("company_id"),
            'kompanies' => Company::all()->sortBy('name'),
            'availableDealsByCompany' => ExclusiveDeal::with('product', 'company')
            ->get()->groupBy("company_id"),
        ]);
    }

    public function storeOrder(Request $request) {
        // dd($request->toArray());
        
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'exclusive_deal_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:100000',
            'status' => 'required|string|in:pending,packed,out for delivery',
            'date_ordered' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'staff_id' => 'nullable|integer|exists:staff,id',
        ]);

        $validated = array_map('strip_tags', $validated);

        // dd($validated);

        Order::insert($validated);

        $creator = auth()->user()->name;
        $user  = User::with('company')->findOrFail($validated['user_id']);

        HistorylogController::add(
            'Add',
            "{$creator} Has made an order for {$user->company->name}."
        );

        return to_route('admin.order')->with('success', 'Order created successfully.');
    }

    public function updateOrder(Request $request, Order $order) 
    {
        DB::beginTransaction();

        try {
            $validate = $request->validate([
                'status' => 'required|string',
                'mother_div' => 'required|string',
                'order_id' => 'required|integer', 
                'staff_id' => 'nullable|integer|exists:staff,id'
            ]);

            $orderId = $validate['order_id'];
            $orderDeets = Order::with(['user.company.location', 'exclusive_deal.product'])->findOrFail($orderId);

            $orderProd = $orderDeets->exclusive_deal->product;
            $orderUser = $orderDeets->user;
            // dd($orderUser->toArray());

            if ($validate['status'] === 'out for delivery' && !empty($validate['staff_id'])) {
                $order->staff_id = $validate['staff_id'];
            }

            if (in_array($validate['status'], ['delivered', 'cancelled'])) {
                $locationId = $orderDeets->user->company->location->id;
                $productId = $orderProd->id;
                $quantity = $orderDeets['quantity'];

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
                    'product_name' => $orderProd->generic_name,
                    'location' => $orderDeets->user->company->location->province,
                    'quantity' => $quantity,
                    'affected_batches' => $affectedBatches,
                    'scanned_at' => now(),
                    'signature' => null,
                ]);

                ImmutableHistory::create([
                    'order_id' => $orderId, 
                    'company_id' => $orderUser->company->id,
                    'user_id' => $orderUser->id,
                    'province' => $orderUser->company->location->province,
                    'company' => $orderUser->company->name,
                    'employee' => $orderUser->name,
                    'date_ordered' => $orderDeets->date_ordered,
                    'status' => $validate['status'],
                    'generic_name' => $orderProd->generic_name,
                    'brand_name' => $orderProd->brand_name,
                    'form' => $orderProd->form,
                    'strength' => $orderProd->strength,
                    'quantity' => $orderDeets->quantity,
                    'price' => $orderDeets->exclusive_deal->price,
                    'subtotal' => $orderDeets->quantity * $orderDeets->exclusive_deal->price,
                ]);
            }

            $order->update(['status' => $validate['status']]);

            HistorylogController::add(
                'Edit',
                "An order from {$orderUser->company->name} in {$orderUser->company->location->province} has been updated to status: " . ucfirst($validate['status'])
            );



            // dd($validate['strength']);

            DB::commit();
            session()->flash("success", 'Order status updated successfully.');

            return to_route('admin.order')->with("update-success", $validate['mother_div']);

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            Log::error("Manual order update failed for Order ID {$request->input('order_id', 'N/A')}: " . $e->getMessage());
            return back()->with("manualUpdateFailed", "Update failed: " . $e->getMessage());
        }
    }
    public function getAvailableStaff(Order $order)
{
    $customerLocationId = $order->user->company->location_id;

    $staff = Staff::where('location_id', $customerLocationId)
                  ->whereNull('archived_at') // Optional: only active staff
                  ->get(['id', 'staff_username', 'email']);

    return response()->json($staff);
}

}
