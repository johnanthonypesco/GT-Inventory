<?php

namespace App\Http\Controllers\Admin;
use App\Models\ImmutableHistory;
use DB;
use Carbon\Carbon;
use Zxing\QrReader;
use App\Models\Order;
use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ScannedQrCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\HistorylogController;


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

        // THE TOTAL INFORMATION FOR EXPIRY
        $totalExpiredStock = Inventory::with(['location', 'product'])->where('quantity', '>', 0)
        ->whereDate('expiry_date', '<', Carbon::now()->toDateString())
        ->orderBy('expiry_date', 'desc')->get();

        $totalNearExpiry = Inventory::with(['location', 'product'])->where('quantity', '>', 0)
        ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addMonth()])
        ->orderBy('expiry_date', 'desc')->get();

        // dd($totalExpiredStock->toArray());

        // DISPLAYED EXPIRY DATA
        $expiredStocks = $totalExpiredStock->groupBy(function ($stocks) {
            return $stocks->location->province;
        });

        $nearExpiredStocks = $totalNearExpiry->groupBy(function ($stocks) {
            return $stocks->location->province;
        });

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
            'stockMonitor' => $inventory = Inventory::has('product')
            ->with('product', 'location') // Ensure 'location' is eager-loaded
            ->get() // Get all inventory records
            ->groupBy('location.province') // First, group by province
            ->map(function ($provinceGroup) { // Map each province group
                return $provinceGroup->groupBy(function ($stock) {
                    return $stock->product->generic_name . '|' . $stock->product->brand_name;
                })
                ->map(function ($group) { // Calculate totals for each product grouping
                    $total = $group->sum('quantity');
                    $status = $total > 50 ? 'in-stock' : ($total > 0 ? 'low-stock' : 'no-stock');

                    return [
                        'total' => $total,
                        'status' => $status,
                        'inventories' => $group,
                    ];
                })->sortKeys();
            }),

        // dd($inventory->toArray());


            // for the stock notifs as wells
            'expiryTotalCounts' => [
                'nearExpiry' => $totalNearExpiry->count(),
                'expired' => $totalExpiredStock->count(),
            ],

            'expiredDatasets' => [
                'nearExpiry' => $nearExpiredStocks,
                'expired' => $expiredStocks,
            ],
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

    public function registerNewProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'generic_name' => 'string|min:3|max:120|nullable',
            'brand_name' => 'string|min:3|max:120|nullable',
            'form' => 'string|min:3|max:120|required',
            'strength' => 'string|min:3|max:120|required',
            'img_file_path' => 'nullable|image|mimes:jpeg,png,jpg|max:30048', // 30MB limit
        ]);

        if ($request->hasFile('img_file_path')) {
            $file = $request->file('img_file_path');
            
            // turn the file name into this kind of format maderpaker >:( = "159357_image.jpg"
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $targetDir = public_path('/products'); 

            if (!is_dir($targetDir)) { // pag wala pang folder sa public
                mkdir($targetDir, 0755, true);
            }
            
            $file->move($targetDir, $filename);

            // ganto na magiging itsura sa DB: "products/159357_image.jpg"
            $validated['img_file_path'] = 'products/' . $filename;
        }

        $validated = array_map('strip_tags', $validated);

        $newProduct = $product->create($validated);

        HistorylogController::addproductlog('Add', 'Product ' . $newProduct->generic_name . ' ' . $newProduct->brand_name . ' has been registered.');

        return to_route('admin.inventory');
    }

    public function editRegisteredProduct(Request $request, Product $product) {
        $validated = $request->validate([
            'id' => 'integer|min:1|required',
            'form_type' => 'string|min:3|required|in:edit-product',
            'generic_name' => 'string|min:3|max:120|nullable',
            'brand_name' => 'string|min:3|max:120|nullable',
            'form' => 'string|min:3|max:120|required',
            'strength' => 'string|min:3|max:120|required',
            'img_file_path' => 'nullable|image|mimes:jpeg,png,jpg|max:30048', // 30MB limit
        ]);

        if ($request->hasFile('img_file_path')) {
            $file = $request->file('img_file_path');
            
            // Deletes the old one
            $oldImage = Product::findOrFail($validated['id'])->img_file_path;
            if ($oldImage && file_exists(public_path($oldImage)) && $oldImage !== 'image/default-product-pic.png') {
                unlink(public_path($oldImage));
            }

            // turn the file name into this kind of format maderpaker >:( = "159357_image.jpg"
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $targetDir = public_path('/products'); 

            if (!is_dir($targetDir)) { // pag wala pang folder sa public
                mkdir($targetDir, 0755, true);
            }
            
            $file->move($targetDir, $filename);

            // ganto na magiging itsura sa DB: "products/159357_image.jpg"
            $validated['img_file_path'] = 'products/' . $filename;
        }

        $validated = array_map('strip_tags', $validated);

        $prod = Product::findOrFail($validated['id']);
        unset($validated['id']); // this will exclude the id in the update array

        $prod->update($validated);

        // dd("updated");
        return to_route('admin.inventory')->with('editProductSuccess', true)->withInput();
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

        HistorylogController::addstocklog('Add', ' ' . $count . ' ' . 'stock(s) for ' . $product->generic_name . ' ' . $product->brand_name . ' has been added.');


        return to_route('admin.inventory');
    }

    public function destroyProduct(Product $product) {
        $product->inventories()->delete(); //SHOULDNT REMOVE STOCK FROM INVENTORY. dont know a solution yet \ (.-.) /
        $product->delete();

        HistorylogController::deleteproductlog('Delete', 'Product ' . $product->generic_name . ' ' . $product->brand_name . ' has been deleted.');
        return to_route("admin.inventory");
    }




    public function deductInventory(Request $request)
    {
        // dd("yeppers");
        try {
            // ✅ Extract Data from Request
            $data = $request->all();
            $orderId     = $data['order_id'] ?? null;
            $productName = $data['product_name'] ?? null;
            $batchNumber = $data['batch_number'] ?? null;
            $expiryDate  = $data['expiry_date'] ?? null;
            $location    = $data['location'] ?? null;
            $quantity    = $data['quantity'] ?? 1;
            $signature   = $request->file('signature'); // Get uploaded signature file

            Log::info("Received QR Data:", $data); // Debug log

            // ✅ Check if QR code has already been scanned
            if (ScannedQrCode::where('order_id', $orderId)->exists()) {
                return response()->json(['message' => '❌ Error: This QR code has already been scanned!'], 400);
            }

            // ✅ Step 1: Get `location_id`
            $locationId = Location::where('province', $location)->value('id');
            if (!$locationId) {
                return response()->json(['message' => '❌ Error: Location "' . $location . '" not found in the database'], 400);
            }

            // ✅ Step 2: Get `product_id`
            $productId = Product::where('generic_name', $productName)->value('id');
            if (!$productId) {
                return response()->json(['message' => '❌ Error: Product "' . $productName . '" not found in the database'], 400);
            }

            // ✅ Step 3: Find inventory using `batch_number` and `expiry_date`
            $inventory = Inventory::where('location_id', $locationId)
                                  ->where('product_id', $productId)
                                  ->where('batch_number', $batchNumber)
                                  ->where('expiry_date', $expiryDate)
                                  ->where('quantity', '>', 0)
                                  ->orderBy('expiry_date', 'asc')
                                  ->orderBy('created_at', 'asc')
                                  ->first();

            if (!$inventory) {
                return response()->json(['message' => '❌ Error: Inventory not found for batch "' . $batchNumber . '" at location "' . $location . '"'], 400);
            }

            // ✅ Step 4: Deduct the quantity
            if ($inventory->quantity >= $quantity) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $quantity
                ]);

                // ✅ Step 5: Update Order Status
                Order::where('id', $orderId)->update([
                    'status'     => 'delivered',
                    'updated_at' => now()
                ]);

                // SIGRAE CODE FOR ARCHIVAL PURPOSES
                $orderArchiveArray = Order::with(['user.company.location', 'exclusivedeal.product'])->findOrFail($orderId)->toArray();
                
                $companyDeets = $orderArchiveArray['user']['company'];
                $province = $orderArchiveArray['user']['company']['location']['province'];
                $employeeDeets = $orderArchiveArray['user'];

                $productDeets = $orderArchiveArray['exclusivedeal']['product'];
                $productPrice = $orderArchiveArray['exclusivedeal']['price'];
                
                ImmutableHistory::createOrFirst([
                    'province' => $province,
                    'company' => $companyDeets["name"],
                    'employee' => $employeeDeets["name"],
                    'date_ordered' => Carbon::parse($orderArchiveArray["date_ordered"])->addDay()->toDateString(), // i added 1 more day because the QR data is somehow behind by 1 day???
                    'status' => $orderArchiveArray["status"],
                    'generic_name' => $productDeets["generic_name"],
                    'brand_name' => $productDeets["brand_name"],
                    'form' => $productDeets["form"],
                    'quantity' => $orderArchiveArray["quantity"],
                    'price' => $productPrice,
                    'subtotal' => $productPrice * $orderArchiveArray["quantity"],
                ]);
                // SIGRAE CODE FOR ARCHIVAL PURPOSES

                // ✅ Step 6: Process and store the signature
                $signaturePath = null;
                if ($signature) {
                    // Generate a filename for the signature
                    $fileName = "signatures/signature_{$orderId}.png";

                    // Store the image manually using Storage::disk('public')
                    Storage::disk('public')->put($fileName, file_get_contents($signature->getRealPath()));

                    // Save only the path in the database
                    $signaturePath = $fileName;
                }

                // ✅ Step 7: Record the scan
                ScannedQrCode::create([
                    'order_id'      => $orderId,
                    'product_name'  => $productName,
                    'batch_number'  => $batchNumber,
                    'expiry_date'   => $expiryDate,
                    'location'      => $location,
                    'quantity'      => $quantity,
                    'scanned_at'    => now(),
                    'signature'     => $signaturePath, // Store signature file path
                ]);

                return response()->json(['message' => '✅ Inventory successfully deducted!'], 200);
            } else {
                return response()->json([
                    'message' => '❌ Error: Not enough stock available. Requested: ' . $quantity . ', Available: ' . $inventory->quantity
                ], 400);
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

                if (Auth::guard('staff')->check()) {
                    return response()->json(['message' => 'Unauthorized: Staff cannot upload QR codes'], 403);
                }
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

                // SIGRAE CODE FOR ARCHIVAL PURPOSES
                $orderArchiveArray = Order::with(['user.company.location', 'exclusivedeal.product'])->findOrFail($orderId)->toArray();
                
                $companyDeets = $orderArchiveArray['user']['company'];
                $province = $orderArchiveArray['user']['company']['location']['province'];
                $employeeDeets = $orderArchiveArray['user'];

                $productDeets = $orderArchiveArray['exclusivedeal']['product'];
                $productPrice = $orderArchiveArray['exclusivedeal']['price'];
                
                ImmutableHistory::createOrFirst([
                    'province' => $province,
                    'company' => $companyDeets["name"],
                    'employee' => $employeeDeets["name"],
                    'date_ordered' => Carbon::parse($orderArchiveArray["date_ordered"])->addDay()->toDateString(), // i added 1 more day because the QR data is somehow behind by 1 day???
                    'status' => $orderArchiveArray["status"],
                    'generic_name' => $productDeets["generic_name"],
                    'brand_name' => $productDeets["brand_name"],
                    'form' => $productDeets["form"],
                    'quantity' => $orderArchiveArray["quantity"],
                    'price' => $productPrice,
                    'subtotal' => $productPrice * $orderArchiveArray["quantity"],
                ]);
                // SIGRAE CODE FOR ARCHIVAL PURPOSES


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

        public function transferInventory(Request $request)
        {
            try {
                // Log the received request data
                \Log::info("Received Transfer Request:", $request->all());

                // Validate the request
                $validated = $request->validate([
                    'inventory_id' => 'required|exists:inventories,inventory_id',
                    'new_location' => 'required' // We will check if it's an ID or a name
                ]);

                // Check if new_location is an ID or a province name
                if (!is_numeric($validated['new_location'])) {
                    // If it's a province name, fetch the corresponding ID
                    $location = Location::where('province', $validated['new_location'])->first();
                    if (!$location) {
                        return response()->json(['success' => false, 'message' => 'Location not found.'], 400);
                    }
                    $validated['new_location'] = $location->id; // Replace name with ID
                }

                // Find the inventory record
                $inventory = Inventory::where('inventory_id', $validated['inventory_id'])->first();
                if (!$inventory) {
                    return response()->json(['success' => false, 'message' => 'Inventory not found.'], 404);
                }

                // Update the inventory's location_id
                $inventory->update(['location_id' => $validated['new_location']]);

                return response()->json([
                    'success' => true,
                    'message' => 'Inventory successfully transferred!'
                ], 200);

            } catch (\Exception $e) {
                \Log::error("Error transferring inventory", ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        }

    }
