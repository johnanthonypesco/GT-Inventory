<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //
    public function showInventory()
    {
        return view('admin.inventory', [
            'products' => Product::all(),
            'inventories' => Inventory::with('product')->get(),
            
            'inStocks' => Inventory::where('quantity', '>', 100)
            ->has("product")
            ->with("product")->get(),

            'lowStocks' => Inventory::where('quantity', '<', 100)
            ->where("quantity", ">", 0)
            ->has("product")
            ->with("product")->get(),

            'outOfStocks' => Inventory::where('quantity', '==', 0)
            ->has("product")
            ->with("product")->get(),
        ]);
    }

    public function addStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'batch_number' => 'string|required',
            'product_id' => 'integer|min:1|required',
            'expiry_date' => 'string|max:120|required',
            'quantity' => 'integer|min:1|required',
            'img_file_path' => 'string|min:3|nullable',
        ]); # defense against SQL injections

        $validated = array_map('strip_tags', $validated); # defense against XSS

        $product = Product::findOrFail($validated['product_id']);

        $product->inventories()->create($validated);

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
