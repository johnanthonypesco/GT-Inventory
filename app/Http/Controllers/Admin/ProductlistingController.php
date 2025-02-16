<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductlistingController extends Controller
{
    //
    public function showProductListingPage(){
        return view('admin.productlisting');
    }
}
