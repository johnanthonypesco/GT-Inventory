<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentmanagementController extends Controller
{
    public function showContentmanagement()
    {
        return view('admin.contentmanagement');
    }
}
