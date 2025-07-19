<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffDetailController extends Controller
{
    /**
     * Get the authenticated staff user's details.
     */
    public function user(Request $request)
    {
        // Get the authenticated staff member
        $staff = $request->user();

        // Return a structured JSON response with only the necessary data
        return response()->json([
            'id' => $staff->id,
            'staff_username' => $staff->staff_username,
            'email' => $staff->email,
            'job_title' => $staff->job_title,
        ]);
    }
}