<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth; // ✅ ADD THIS LINE

class MobileCustomerReview extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
            'allow_public_display' => 'required|boolean', // ✅ Recommended: Add validation
        ]);

        Review::create([
            'user_id' => Auth::id(), // This will now work correctly
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment'],
            'allow_public_display' => $validatedData['allow_public_display'],
        ]);

        return response()->json(['success' => 'Thank you for your feedback!']);
    }
}