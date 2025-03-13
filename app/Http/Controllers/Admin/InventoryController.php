<?php

namespace App\Http\Controllers\Admin;

use Zxing\QrReader;
use App\Models\Order;
use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ScannedQrCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class InventoryController extends Controller
{

    public function showInventory
    (
        $searched_name = null, //product name
        $form_data = null,  // results ng sinearch 
        $search_type = null, // stock = yung una na table. product = yung nasa loob ng "view products" modal
        $location_filter = 'All', // Ginagamit ng dropdown location select & search function
    )
    {
        $inventory = Inventory::with('product')->select()->get();

        // This switch is for the dropdown select tag for the locations
        switch ($location_filter) {
            case 'Tarlac':
                $inventory = Inventory::with(['product', 'location'])->where('location_id', 1)
                ->orderBy('expiry_date', 'desc')
                ->get()->groupBy(fn ($stock) => $stock->location_id);
                break;
 
            case 'Nueva Ecija':
                $inventory = Inventory::with(['product', 'location'])->where('location_id', 2)
                ->orderBy('expiry_date', 'desc')
                ->get()->groupBy(fn ($stock) => $stock->location_id);
                break;

            default:
                $inventory = Inventory::with(['product', 'location'])
                ->orderBy('expiry_date', 'desc')
                ->get()->groupBy(function ($stock) {
                    return $stock->location_id;
                });
                break;
        }

        // SEARCH SUGGESTIONS FOR SPECIFC PROVINCES
        $suggestionsForTarlac = Inventory::where('location_id', Location::where('province', 'Tarlac')->first()->id)->get();
        $suggestionsForNueva = Inventory::where('location_id', Location::where('province', 'Nueva Ecija')->first()->id)->get();

        return view('admin.inventory', [
            'products' => Product::all(),

            'registeredProducts' => $search_type === 'product' ? $form_data : Product::all(),
            
            // if the user searches something it will provide the data from the searched result instead
            'inventories' => $search_type === 'stock' ? $form_data : $inventory,
            'current_inventory' => $location_filter,

            'locations' => Location::all(),

            // The current state of the page (Dito sinesetup lahat ng filters)
            'currentSearch' => ['query' => $searched_name, 'type' => $search_type, 'location' => $location_filter],
            
            // SEARCH SUGGESTIONS FOR THE STOCK SEARCH BAR
            'tarlacSuggestions' => $suggestionsForTarlac,
            'nuevaSuggestions' => $suggestionsForNueva,

            // for the inventory stock notifications
            'stockMonitor' => Inventory::has('product') 
            ->with('product')->get() // gets the product data
            ->groupBy(function ($inventory) { // groups data by generic name
                return $inventory->product->generic_name . '|' . $inventory->product->brand_name; // this gives the name for the keys of each group
            })->sortKeys()
            ->map(function ($group) { // calculates the totals and what to categorize them as.
                $total = $group->sum('quantity');
                $status = $total > 0 && $total <= 50 ? 'low-stock' : ($total > 50 ? 'in-stock' : 'no-stock');
                return [
                    'total' => $total,
                    'status' => $status,
                    'inventories' => $group,
                ];
            }),
        ]);
    }

    // the function handles the form inside the location dropdown
    public function showInventoryLocation(Request $request) {
        $validated = $request->validate([
            'location' => 'required|string',
        ]);

        $validated = array_map('strip_tags', $validated);
        
        switch ($validated['location']){
            case 'Tarlac':
                return $this->showInventory(location_filter:"Tarlac");

            case 'Nueva Ecija':
                return $this->showInventory(location_filter:"Nueva Ecija");
            default:
                return $this->showInventory(location_filter:"All");
        }
    }

    public function searchInventory(Request $request, $type) {
        $validated = $request->validate([
            'search' => 'string|min:5|required',
            'location_filter' => 'string|required',
        ]);

        $validated = array_map('strip_tags',$validated);

        // splits up the string and returns an array
        $validatedSearch = explode(' - ',$validated['search']);

        
        if($type === "stock") {
            $location_id = Location::where('province', $validated['location_filter'])
            ->select('id')->first()->id;

            // makes the where query on the Products table instead of the Inventory table
            $result =  Inventory::with(['product', 'location'])
            ->where('location_id', $location_id)
            ->whereHas('product', function ($query) use ($validatedSearch) {
                $query->where('generic_name', '=', $validatedSearch[0])
                ->where('brand_name', '=', $validatedSearch[1]);
            })
            ->get();
    
            // dd($validated['location_filter']);
            return $this->showInventory($validatedSearch,$result, "stock", $validated['location_filter']);
        } 
        elseif ($type === "product") {
            $result = Product::where('generic_name', '=', $validatedSearch[0])
            ->where('brand_name', '=', $validatedSearch[1])
            ->get();
            
            return $this->showInventory($validatedSearch, $result, 'product'); 
        }
    }

    public function registerNewProduct(Request $request, Product $product){
        $validated = $request->validate([
            'generic_name' => 'string|min:3|max:120|nullable',
            'brand_name' => 'string|min:3|max:120|nullable',
            'form' => 'string|min:3|max:120|required',
            'strength' => 'string|min:3|max:120|required',
            'img_file_path' => 'string|min:3|nullable',
        ]); # defense against SQL injections

        $validated = array_map('strip_tags', $validated); # defense against XSS

        $product->create($validated);

        return to_route('admin.inventory');
    }

    public function addStock(Request $request, $addType = null)
    {
        // I'm using Validator to differentiate the two types of add stock forms, para mahiwalay yung re-popup.
        $validator = Validator::make($request->all(), [
            'batch_number.*' => 'string|min:3|required',
            'product_id.*' => 'integer|min:1|required|exists:products,id',
            'location_id.*' => 'integer|min:1|required|exists:locations,id',
            'expiry_date.*' => 'date|required',
            'quantity.*' => 'integer|min:1|max:100000|required',
            'img_file_path.*' => 'string|min:3|nullable',
        ]); # defense against SQL injections

        if($validator->fails()) {
            return back()
            ->withErrors($validator) // allows us to use $errors
            ->withInput() // allows us to use old()
            ->with('stockFailType', $addType); // allows us to use session('stockFailType')
        }

        $validated = $validator->validated(); // returns the validated values

        // this is anti-XSS if the validated array is one dimensional:
        // $validated = array_map('strip_tags', $validated); # defense against XSS

        // this is anti-XSS if the validated array has nested arrays:
        // the &$value refereneces the actual value in the array, and hindi na natin need gamitin yung $key
        array_walk_recursive($validated, function (&$value, $key) {
            $value = strip_tags($value);
        });

        $count = count($validated['product_id']);

        for ($i=0 ; $i < $count ; $i++) {
            $datas = [
                'batch_number' => $validated['batch_number'][$i],
                'quantity' => $validated['quantity'][$i],
                'expiry_date' => $validated['expiry_date'][$i],
                'location_id' => $validated['location_id'][$i],
            ];

            $product = Product::findOrFail($validated['product_id'][$i]);
    
            $product->inventories()->create($datas);
        }


        return to_route('admin.inventory');
    }

    public function destroyProduct(Product $product) {
        $product->inventories()->delete(); //SHOULDNT REMOVE STOCK FROM INVENTORY. dont know a solution yet \ (.-.) /
        $product->delete();

        return to_route("admin.inventory");
    }



  
    public function deductInventory(Request $request)
    {
        try {
            // Extract data from QR code
            $data = $request->all();
    
            $orderId     = $data['order_id'] ?? null;
            $productName = $data['product_name'] ?? null;
            $batchNumber = $data['batch_number'] ?? null;
            $expiryDate  = $data['expiry_date'] ?? null;
            $location    = $data['location'] ?? null;
            $quantity    = $data['quantity'] ?? 1;
    
            Log::info("Received QR Data:", $data); // Debug log

            if (ScannedQrCode::where('order_id', $orderId)->exists()) {
                return response()->json(['message' => ' Error: This QR code has already been scanned!'], 400);
            }
        
            // Step 1: Get `location_id`
            $locationId = Location::where('province', $location)->value('id');
    
            if (!$locationId) {
                return response()->json(['message' => 'Error: Location "' . $location . '" not found in the database'], 400);
            }
    
            // Step 2: Get `product_id`
            $productId = Product::where('generic_name', $productName)->value('id');
    
            if (!$productId) {
                return response()->json(['message' => 'Error: Product "' . $productName . '" not found in the database'], 400);
            }
    
            // Step 3: Find inventory using `batch_number` and `expiry_date`
            $inventory = Inventory::where('location_id', $locationId)
                                  ->where('product_id', $productId)
                                  ->where('batch_number', $batchNumber)
                                  ->where('expiry_date', $expiryDate)
                                  ->where('quantity', '>', 0)
                                  ->orderBy('expiry_date', 'asc')
                                  ->orderBy('created_at', 'asc')
                                  ->first();
    
            if (!$inventory) {
                return response()->json(['message' => 'Error: Inventory not found for batch "' . $batchNumber . '" at location "' . $location . '"'], 400);
            }
    
            // Step 4: Deduct the quantity
            if ($inventory->quantity >= $quantity) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $quantity
                ], ['inventory_id' => $inventory->inventory_id]); // Fix: Use inventory_id instead of id

                Order::where('id', $orderId)->update([
    'status'     => 'delivered',
    'updated_at' => now()
]);
    
                // Step 5: Record the scan
                ScannedQrCode::create([
                    'order_id'      => $orderId,
                    'product_name'  => $productName,
                    'batch_number'  => $batchNumber,
                    'expiry_date'   => $expiryDate,
                    'location'      => $location,
                    'quantity'      => $quantity,
                    'scanned_at'    => now(),
                ]);
    
                return response()->json(['message' => '✅ Inventory successfully deducted!'], 200);
            } else {
                return response()->json(['message' => 'Error: Not enough stock available. Requested: ' . $quantity . ', Available: ' . $inventory->quantity], 400);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Server error: ' . $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }


   
        public function uploadQrCode(Request $request)
        {
            try {
                // Validate uploaded QR code image
                $request->validate([
                    'qr_code' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]);
    
                // Store uploaded image temporarily
                $path = $request->file('qr_code')->store('temp_qr_codes');
    
                // Read QR code data
                $qrReader = new QrReader(storage_path('app/private/' . $path));
                $qrData = $qrReader->text();
    
                // Delete temp file
                // unlink(storage_path('app/public' . $path));
    
                if (!$qrData) {
                    return response()->json(['message' => 'Error: Unable to read QR code.'], 400);
                }
    
                // Convert JSON string to array
                $data = json_decode($qrData, true);
    
                if (!$data) {
                    return response()->json(['message' => 'Error: Invalid QR code format.'], 400);
                }
    
                Log::info("Uploaded QR Code Data:", $data);
    
                // Extract order details
                $orderId     = $data['order_id'] ?? null;
                $productName = $data['product_name'] ?? null;
                $batchNumber = $data['batch_number'] ?? null;
                $expiryDate  = $data['expiry_date'] ?? null;
                $location    = $data['location'] ?? null;
                $quantity    = $data['quantity'] ?? 1;
    
                // Check if QR code was already scanned
                if (ScannedQrCode::where('order_id', $orderId)->exists()) {
                    return response()->json(['message' => '⚠️ Error: This QR code has already been used!'], 400);
                }
    
                // Get `location_id`
                $locationId = Location::where('province', $location)->value('id');
    
                if (!$locationId) {
                    return response()->json(['message' => 'Error: Location not found'], 400);
                }
    
                // Get `product_id`
                $productId = Product::where('generic_name', $productName)->value('id');
    
                if (!$productId) {
                    return response()->json(['message' => 'Error: Product not found'], 400);
                }
    
                // Find inventory
                $inventory = Inventory::where('location_id', $locationId)
                                      ->where('product_id', $productId)
                                      ->where('batch_number', $batchNumber)
                                      ->where('expiry_date', $expiryDate)
                                      ->where('quantity', '>', 0)
                                      ->orderBy('expiry_date', 'asc')
                                      ->orderBy('created_at', 'asc')
                                      ->first();
    
                if (!$inventory) {
                    return response()->json(['message' => 'Error: Inventory not found'], 400);
                }
    
                // Deduct the quantity
                // Inventory::where('inventory_id', $inventory->inventory_id)->update([
                //     'quantity'   => $inventory->quantity - $quantity,
                //     'updated_at' => now()
                // ]);

                 if ($inventory->quantity >= $quantity) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $quantity
                ], ['inventory_id' => $inventory->inventory_id]);
                }
    
                Order::where('id', $orderId)->update([
                    'status'     => 'delivered',
                    'updated_at' => now()
                ]);

                // Record the scan
                ScannedQrCode::create([
                    'order_id'      => $orderId,
                    'product_name'  => $productName,
                    'batch_number'  => $batchNumber,
                    'expiry_date'   => $expiryDate,
                    'location'      => $location,
                    'quantity'      => $quantity,
                    'scanned_at'    => now(),
                ]);
    
                return response()->json(['message' => '✅ Inventory successfully deducted!'], 200);
    
            } catch (\Exception $e) {
                return response()->json([
                    'message' => '❌ Server error: ' . $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile()
                ], 500);
            }
        }
    }
    