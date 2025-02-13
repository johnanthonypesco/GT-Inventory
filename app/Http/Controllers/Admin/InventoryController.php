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
}
