<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageaccountController extends Controller
{
    //
    public function showAccount(){
        return view('customer.manageaccount');
    }
}
