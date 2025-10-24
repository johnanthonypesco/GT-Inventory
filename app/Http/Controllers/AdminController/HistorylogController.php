<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistorylogController extends Controller
{
    public function showhistorylog()
    {
        return view('admin.historylog');
    }
}
