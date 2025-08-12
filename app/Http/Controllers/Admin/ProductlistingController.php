<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExclusiveDeal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HistorylogController;
use App\Models\Location;
use Illuminate\Support\Str;

class ProductlistingController extends Controller
{    
    public function showProductListingPage(Request $request){        
        $search_type = $request->input('search_type', null);
        $current_search = $request->input('current_search', null);
        $specificCompany = $request->input('specific_company_deal', null);

        $perPage = 10;
        $locations = [];
        $dealsDB = collect(); 

        Location::with(['companies'])->orderBy('province')->get()
        // if a var has & in it, it's referencing the variable id, 
        // without it its just getting the value from the OG
        ->each(function ($loc) use (
            &$locations,
            &$dealsDB, 
            $request, 
            $perPage, 
            $current_search, 
            $search_type,
            $specificCompany,
            ) {

            // Gagawa ito ng bago collection sa array for the current province in the $locations array
            $locations[$loc->province] = collect();

            // Kukunin lahat ng companies sa location nayun yung foreach nato
            foreach ($loc->companies as $company) {
                // Need unique with no spaces ito para ma differentiate yung pages sa URL query (paginating paking shit)
                $pageKey = 'pg_in_' . Str::slug($loc->province) . '_' . Str::slug($company->name);

                $deals = ExclusiveDeal::where('company_id', $company->id);

                // may nag saearch ng product deal add another query condition
                if ($current_search !== null && $search_type === "deal" && $specificCompany === $company->name) {

                    $validatedSearch = explode(' - ',$current_search);

                    $deals = $deals->whereHas('product', 
                        function ($query) use ($validatedSearch) {
                        $query->where('generic_name', '=', $validatedSearch[0])
                        ->where('brand_name', '=', $validatedSearch[1])
                        ->where('form', $validatedSearch[2])
                        ->where('strength', $validatedSearch[3]);
                    });
                }

                $deals = $deals->paginate($perPage, ['*'], $pageKey)
                ->appends(array_merge(
                    $request->except($pageKey), // Keep existing query params
                    ['reSummon' => $company->name] // Force company modal to re-open
                ));
                

                // Add current company to province's collection in $locations
                $locations[$loc->province]->push($company);

                $dealsDB[$company->name] = $deals;
            }
        });

        // IF MAY SEARCH FILTER THE UNWANTED BUGGERS
        if ($current_search !== null & $search_type === "company") {
            foreach ($locations as $province => $companies) {
                $filteredCompanies = $companies->filter(function ($company) use ($current_search) {
                    return stripos($company->name, $current_search) !== false;
                });

                // If no companies left after filter, remove province entirely
                if ($filteredCompanies->isEmpty()) {
                    unset($locations[$province]);
                } else {
                    $locations[$province] = $filteredCompanies;
                }
            }

            // filter $dealsDB to only keep those matching
            $dealsDB = $dealsDB->filter(function ($_, $companyName) use ($current_search) {
                return stripos($companyName, $current_search) !== false;
            });
        }

        // dd($dealsDB->toArray());

        return view('admin.productlisting', [
            'locations' => $locations,
            'dealsDB'   => $dealsDB,
            
            'products'  => Product::all()->sortBy('generic_name'),
            'companySearchSuggestions' => Company::get("name"),

            'current_search' => [
                'query' => $current_search,
                'deal_company' => $specificCompany,
                'type' => $search_type
            ],
        ]);
    }

    public function createExclusiveDeal(Request $request) {
        // this shit will accept singular and multpile creations
        $validated = $request->validate([
            "company_id"    => 'required|integer',
            "product_id"    => 'required',
            "product_id.*"  => 'integer',
            "price"         => 'required',
            "price.*"       => 'numeric|max:100000',
        ]);

        // turn it all into arrays so singular and multiple are equals
        foreach (['product_id', 'price'] as $key) {
            if (!is_array($validated[$key])) {
                $validated[$key] = [$validated[$key]];
            }
        }

        // Sanitize all
        array_walk_recursive($validated, function (&$val) {
            $val = strip_tags($val);
        });

        // Loop through and create deals
        $company = Company::findOrFail($validated['company_id']);

        for ($i = 0; $i < count($validated['product_id']); $i++) {
                $product = Product::findOrFail($validated['product_id'][$i]);

                ExclusiveDeal::create([
                    'company_id' => $company->id,
                    'product_id' => $product->id,
                    'price'      => $validated['price'][$i],
                ]);
            

            HistorylogController::adddealslog(
                "Add",
                "Add deals " . $company->name,
                $company->id,
                $product->id
            );
        }
    
    
        // redirecting to previous will keep the damn pagination url params
        return redirect()->to(url()->previous())->with('success', 'Deal(s) created successfully.');
    }

    public function updateExclusiveDeal(Request $request, $aidee = 0) {
        $validated = $request->validate([
            "company" => 'string|required|min:1',
            "price" => 'numeric|required|min:1',
        ]);

        $validated = array_map("strip_tags", $validated);

        // ExclusiveDeal::findOrFail($aidee)->update($validated);

        //gawa ni anthony cinomment ko muna sayo kapag may problem balik mo
        $exclusiveDeal = ExclusiveDeal::findOrFail($aidee);
        $exclusiveDeal->update($validated);
        
        HistorylogController::editdealslog("Edit", "Edit deals of product:" . $exclusiveDeal->product->generic_name . " in company " . $exclusiveDeal->company->name, $exclusiveDeal->product_id);
        //gawa ni anthony

        session()->flash('success', 'Deal updated successfully.');

        // redirecting to previous will keep the damn pagination url params
        return redirect()->to(url()->previous())
        ->with([
            'edit-success' => true,
            'company-success' => $validated['company'],
        ]);
    }

    public function destroyExclusiveDeal($deal_id = null, $company = null) {
        $exclusiveDeal = ExclusiveDeal::findOrFail($deal_id);
        
        $product = Product::find($exclusiveDeal->product_id);
        $company = Company::find($exclusiveDeal->company_id);
    
        if (!$product || !$company) {
            return back()->withErrors(['error' => 'Product or company not found.']);
        }
    
        $exclusiveDeal->delete();
    
        

        // gawa ni pesco
        HistorylogController::deleteproductlog(
            "Delete",
            "Deleted product " . $product->generic_name . " in company " . $company->name,
            $product->id
        );
        // gawa ni pesco
    
        session()->flash('success', 'Deal deleted successfully.');

        return redirect()->to(url()->previous())->with('reSummon', $company->name);
    }
    
}
