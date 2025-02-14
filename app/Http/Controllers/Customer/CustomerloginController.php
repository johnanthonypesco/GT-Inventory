<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerloginController extends Controller
{
    //
    public function showLogin(){
        return view('customer.login');
    }
}
