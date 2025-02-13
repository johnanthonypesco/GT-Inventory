<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductlistingController extends Controller
{
    //
    public function showProductListingPage(){
        return view('admin.productlisting', [
            'listedProducts' => Product::all(),
        ]);
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

        return to_route('admin.productlisting');
    }
}
