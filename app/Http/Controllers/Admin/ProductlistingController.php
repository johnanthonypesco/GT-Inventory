<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductlistingController extends Controller
{    
    public function showProductListingPage(){
        $customers = User::all();
        $products = Product::all();

        return view('admin.productlisting', [
            "customers" => $customers,
            "products" => $products,

            "dealsDB" => ExclusiveDeal::with("user")->get()
            ->groupBy(function ($deal) {
                return $deal->user->name;
            })->sortKeys(),
        ]);
    }

    public function createExclusiveDeal(Request $request) {
        $validated = $request->validate([
            "user_id" => 'required|integer',
            "product_id" => 'required|integer',
            "deal_type" => 'string|nullable',
            "price" => 'numeric|required|max:50000',
        ]);

        $validated = array_map("strip_tags", $validated);


        ExclusiveDeal::create($validated);

        return to_route('admin.productlisting');
    }

    public function destroyExclusiveDeal($deal_id = null, $user = null) {
        ExclusiveDeal::findOrFail($deal_id)->delete();

        return to_route('admin.productlisting')->with('reSummon', $user);
    }
}
