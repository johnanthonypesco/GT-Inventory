<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HistorylogController;

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
        // Log the approval action
        HistorylogController::reviewmanagerlog('Approve', 'Review of Customer:' . $review->user->name . ' has been approved by ');
        return back()->with('success', 'Review approved for display.');

    }

    public function disapprove(Review $review)
    {
        $review->update(['is_approved' => false]);
        HistorylogController::disapprovereviewlog('Disapprove', 'Review of Customer: ' . $review->user->name . ' has been disapproved by ' );
        return back()->with('success', 'Review hidden from public.');
        // Log the disapproval action
    }
}
