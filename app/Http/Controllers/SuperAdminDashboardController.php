<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the Super Admin Dashboard.
     */
    public function index()
    {
        return view('superadmin.superadmin-dashboard'); // ✅ Loads the correct view
    }
}
