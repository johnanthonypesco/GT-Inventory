<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductMovementController extends Controller
{
    public function showproductmovement()
    {
        return view('admin.productmovement');
    }
}
