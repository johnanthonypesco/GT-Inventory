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
    /**
     * Display the main history log page for the initial load.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
     public function showHistorylog(Request $request)
    {
        // Get the initial set of logs for the first page load.
        $historylogs = Historylogs::orderBy('created_at', 'desc')->paginate(10);

        // *** THIS IS THE FIX ***
        // Manually set the path for pagination links to point to the AJAX route.
        // Make sure 'admin.historylog.search' is the correct name of your route in web.php.
        $historylogs->withPath(route('admin.historylog.search'));
        
        // Pass the paginated data to the main view.
        return view('admin.historylog', [
            'historylogs' => $historylogs,
            'currentPage' => $historylogs->currentPage(),
            'totalPage' => $historylogs->lastPage(),
            'prevPageUrl' => $historylogs->previousPageUrl(),
            'nextPageUrl' => $historylogs->nextPageUrl(),
        ]);
    }

    /**
     * Handle AJAX requests for searching, filtering, and paginating history logs.
     * Returns a partial view containing only the table data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function searchHistorylog(Request $request)
    {
        $query = Historylogs::query();

        // Apply search filter if a search term is provided.
        if ($request->filled('search')) {
            $search = $request->input('search');
            // Search across multiple relevant columns for better results.
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('event', 'like', '%' . $search . '%')
                  ->orWhere('user_email', 'like', '%' . $search . '%');
            });
        }

        // Apply event type filter if a specific event is selected.
        if ($request->filled('event') && $request->input('event') != 'All') {
            $event = $request->input('event');
            $query->where('event', $event);
        }

        // Order results by the newest first and paginate.
        $historylogs = $query->orderBy('created_at', 'desc')->paginate(10);

        // IMPORTANT: Append the search and filter parameters to pagination links
        // so that filters are maintained when navigating pages.
        $historylogs->appends($request->all());

        // Return the partial view with the filtered and paginated data.
        return view('admin.partials.historylog_table', [
            'historylogs' => $historylogs,
            'currentPage' => $historylogs->currentPage(),
            'totalPage' => $historylogs->lastPage(),
            'prevPageUrl' => $historylogs->previousPageUrl(),
            'nextPageUrl' => $historylogs->nextPageUrl(),
        ]);
    }

    // add Product log   
    public static function addproductlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // edit Product log
    public static function editproductlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // add Stock log
    public static function addstocklog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // delete Product log
    public static function deleteproductlog($event, $description)
    {
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

    // edit deals log
    public static function editdealslog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
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
    public static function addaccountlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // edit account log
    public static function editaccountlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // delete account log
    public static function deleteaccountlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // add content log
    public static function addcontentlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //display product log
    public static function displayproductlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //review manager log
    public static function reviewmanagerlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //disapprove review log
    public static function disapprovereviewlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //transfer product log
    public static function transferproductlog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    //edit stock log
    public static function editstocklog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    // change order status log
    public static function changeorderstatuslog($event, $description)
    {
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

}