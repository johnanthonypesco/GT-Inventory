<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageaccountController extends Controller
{
    //
    public function showManageaccount(){
        return view('admin.manageaccount');
    }
}
