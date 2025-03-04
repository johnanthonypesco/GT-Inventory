<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{

    public function showInventory
    (
        $searched_name = null, 
        $form_data = null, 
        $search_type = null,
        $location_filter = 'All',
    )
    {
        $inventory = Inventory::with('product')->select()->get();

        switch ($location_filter) {
            case 'Tarlac':
                $inventory = Inventory::with('product')->where('location_id', 1)
                ->orderBy('expiry_date', 'desc')
                ->get()->groupBy(fn ($stock) => $stock->location_id);
                break;
 
            case 'Nueva Ecija':
                $inventory = Inventory::with('product')->where('location_id', 2)
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

        return view('admin.inventory', [
            'products' => Product::all(),
            'registeredProducts' => $search_type === 'product' ? $form_data : Product::all(),
            
            'inventories' => $search_type === 'stock' ? $form_data : $inventory,
            'current_inventory' => $location_filter,

            'locations' => Location::all(),

            'currentSearch' => ['query' => $searched_name, 'type' => $search_type],

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
        ]);

        $validated = array_map('strip_tags',$validated);

        // splits up the string and returns an array
        $validated = explode(' - ',$validated['search']);

        if($type === "stock") {
            // makes the where query on the Products table instead of the Inventory table
            $result =  Inventory::with('product')
            ->whereHas('product', function ($query) use ($validated) {
                $query->where('generic_name', '=', $validated[0])
                ->where('brand_name', '=', $validated[1]);
            })
            ->get();
    
            return $this->showInventory($validated,$result, "stock");
        } 
        elseif ($type === "product") {
            $result = Product::where('generic_name', '=', $validated[0])
            ->where('brand_name', '=', $validated[1])
            ->get();
            
            return $this->showInventory($validated, $result, 'product'); 
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
}