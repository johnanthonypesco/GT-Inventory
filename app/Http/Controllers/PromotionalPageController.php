<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromotionalPageController extends Controller
{
    public function showPromotionalPage()
    {
        return view('index');
    }
}
