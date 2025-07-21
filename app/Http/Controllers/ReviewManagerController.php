<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewManagerController extends Controller
{
    public function index()
    {
        $reviews = Review::latest()->get();
        return view('admin.reviewmanager', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved for display.');
    }

    public function disapprove(Review $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Review hidden from public.');
    }
}
