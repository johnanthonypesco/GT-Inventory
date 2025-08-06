<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Historylogs;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Product;
use App\Models\ManageContents;
class HistorylogController extends Controller
{
    public function showHistorylog(Request $request) {
    // Start with a base query
    $query = Historylogs::query();

    // Check if there is a search keyword
    if ($request->has('search') && $request->input('search') != '') {
        $search = $request->input('search');
        // Add a where clause to search in the description or other relevant columns
        $query->where('description', 'like', '%' . $search . '%');
    }

    // Check if there is an event filter and it's not 'All'
    if ($request->has('event') && $request->input('event') != 'All') {
        $event = $request->input('event');
        $query->where('event', $event);
    }

    // Order by the newest logs and paginate the results
    $historylogs = $query->orderBy('created_at', 'desc')->paginate(10);

    // Append the search and filter parameters to the pagination links
    // This ensures that when you go to the next page, the filters are still applied
    $historylogs->appends($request->all());

    return view('admin.historylog', [
        'historylogs' => $historylogs,
        'currentPage' => $historylogs->currentPage(),
        'totalPage' => $historylogs->lastPage(),
        'prevPageUrl' => $historylogs->previousPageUrl(),
        'nextPageUrl' => $historylogs->nextPageUrl(),
    ]);
}
    

    // add Product log  
    public static function addproductlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // edit Product log
    public static function editproductlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // add Stock log
    public static function addstocklog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //ocr scanned log
    // public static function ocrscannedlog($event, $description){
    //     Historylogs::create([
    //         'event' => $event,
    //         'description' => $description,
    //         'user_email'=> auth()->user()->email,
    //         'created_at' => now()
    //     ]);
    // }

    // // transfer log
    // public static function transferlog($event, $description){
    //     Historylogs::create([
    //         'event' => $event,
    //         'description' => $description,
    //         'user_email'=> auth()->user()->email,
    //         'created_at' => now()
    //     ]);
    // }

    // delete Product log
    public static function deleteproductlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // add deals log
    public static function adddealslog($event, $description, $companyId, $productId)
    {
        $company = Company::find($companyId);
        $product = Product::find($productId);

        if (!$company || !$product) {
            return; 
        }
        
        Historylogs::create([
            'event' => $event,
            'description' => "$description Product: {$product->generic_name} in Company: {$company->name}",
            'user_email' => auth()->user()->email ?? 'System',
            'created_at' => now(),
        ]);
    }

    // delete deals log
    public static function deletedealslog($event, $description, $companyId, $productId)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email' => auth()->user()->email ?? 'System',
            'created_at' => now(),
        ]);
    }

    // add account log
    public static function addaccountlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // edit account log
    public static function editaccountlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // delete account log
    public static function deleteaccountlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // add content log
    public static function addcontentlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //review manager log
    public static function reviewmanagerlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,            
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //disapprove review log
    public static function disapprovereviewlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //transfer product log
    public static function transferproductlog($event, $description, $productId, $locationName)
    {
        $product = Product::find($productId);

        if (!$product) {
            return; 
        }

        Historylogs::create([
            'event' => $event,
            'description' => "$description Product: {$product->generic_name} to Location: {$locationName}",
            'user_email' => auth()->user()->email,
            'created_at' => now(),
        ]);
    }

    //edit stock log quantity and expiry
    public static function editstocklog($event, $description, $productId, $locationName)
    {
        $product = Product::find($productId);

        if (!$product) {
            return; 
        }

        Historylogs::create([
            'event' => $event,
            'description' => "$description Product: {$product->generic_name} at Location: {$locationName}",
            'user_email' => auth()->user()->email,
            'created_at' => now(),
        ]);
    }
    
}
