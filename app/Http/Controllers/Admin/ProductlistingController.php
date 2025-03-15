<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExclusiveDeal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HistorylogController;

class ProductlistingController extends Controller
{    
    public function showProductListingPage(){
        $companies = Company::with('location')->get()
        ->groupBy(function ($companies) {
            return $companies->location->province;
        });
        $products = Product::all();

        return view('admin.productlisting', [
            "locations" => $companies,
            "products" => $products,

            "dealsDB" => ExclusiveDeal::with("company")->get()
            ->groupBy(function ($deal) {
                return $deal->company->name;
            })->sortKeys(),
        ]);
    }

    public function createExclusiveDeal(Request $request) {
        $validated = $request->validate([
            "company_id" => 'required|integer',
            "product_id" => 'required|integer',
            "deal_type" => 'string|nullable',
            "price" => 'numeric|required|max:50000',
        ]);
    
        $validated = array_map("strip_tags", $validated);
    
        $company = Company::findOrFail($validated['company_id']);
        $product = Product::findOrFail($validated['product_id']);
    
        ExclusiveDeal::create($validated);
    
        HistorylogController::adddealslog(
            "Add",
            "Add deals " . $company->name,
            $company->id,
            $product->id
        );
    
        return to_route('admin.productlisting');
    }
    
    public function destroyExclusiveDeal($deal_id = null, $company = null) {
        $exclusiveDeal = ExclusiveDeal::findOrFail($deal_id);
        
        $product = Product::find($exclusiveDeal->product_id);
        $company = Company::find($exclusiveDeal->company_id);
    
        if (!$product || !$company) {
            return back()->withErrors(['error' => 'Product or company not found.']);
        }
    
        $exclusiveDeal->delete();
    
        HistorylogController::deleteproductlog(
            "Delete",
            "Deleted product " . $product->generic_name . " in company " . $company->name,
            $product->id
        );
    
        return to_route('admin.productlisting')->with('reSummon', $company->id);
    }
    
}
