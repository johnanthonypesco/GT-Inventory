<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffLocation;
use Illuminate\Support\Facades\Auth;

class StaffLocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        // dd($request->toArray());

        $staff = Auth::guard('staff')->user();

        if (!$staff) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        StaffLocation::updateOrCreate(
            ['staff_id' => $staff->id],
            ['latitude' => $request->latitude, 'longitude' => $request->longitude]
        );

        return response()->json(['message' => 'Location updated successfully']);
    }

    public function getLocations()
    {
        $locations = StaffLocation::with('staff')->get();
        // dd($locations)->toArray();

    
        if ($locations->isEmpty()) {
            
            return response()->json(['message' => 'No staff locations found.'], 200);

        }
        return response()->json($locations);
    }
    

    public function index()
    {
        return view('stafflocation');
    }
}



