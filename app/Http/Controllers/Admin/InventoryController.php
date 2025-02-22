<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{

    public function showInventory($search_value = null, $form_data = null)
    {
        return view('admin.inventory', [
            'products' => Product::all(),
            'inventories' => $form_data ? $form_data : Inventory::with('product')->select()->get(),

            'current_search' => $search_value,

            'inStocks' => Inventory::where('quantity', '>', 100)
            ->has("product")
            ->with("product")->get(),

            'lowStocks' => Inventory::where('quantity', '<', 100)
            ->where("quantity", ">", 0)
            ->has("product")
            ->with("product")->get(),

            'outOfStocks' => Inventory::where('quantity', '=', 0)
            ->has("product")
            ->with("product")->get(),
        ]);
    }

    public function searchInventory(Request $request) {
        $validated = $request->validate([
            'search' => 'string|min:5|required',
        ]);

        $validated = array_map('strip_tags',$validated);

        $validated = explode(' - ',$validated['search']);

        $result = Inventory::with('product')
        ->whereHas('product', function ($query) use ($validated) {
            $query->where('generic_name', '=', $validated[0])
            ->where('brand_name', '=', $validated[1]);
        })
        ->get();

        return $this->showInventory($validated,$result);
    }

    public function addStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'batch_number.*' => 'string|required',
            'product_id.*' => 'integer|min:1|required|exists:products,id',
            'expiry_date.*' => 'string|max:120|required',
            'quantity.*' => 'integer|min:1|required',
            'img_file_path.*' => 'string|min:3|nullable',
        ]); # defense against SQL injections

        // dd($validated);


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
                'quantity'     => $validated['quantity'][$i],
                'expiry_date'  => $validated['expiry_date'][$i],
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
}
