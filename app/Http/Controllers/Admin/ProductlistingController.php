<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductlistingController extends Controller
{
    //
    public function showProductlisting(){
        return view('admin.productlisting');
    }
}
