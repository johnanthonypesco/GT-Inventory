<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageorderController extends Controller
{
    //
    public function showManageOrder(){
        return view('customer.manageorder');
    }
}
