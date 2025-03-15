<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Historylogs;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Product;

class HistorylogController extends Controller
{
    public function showHistorylog(Request $request){

        $historylogs = Historylogs::orderBy('created_at', 'desc')->get();
        return view('admin.historylog', ['historylogs' => $historylogs]);
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

    // add Stock log
    public static function addstocklog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

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
}
