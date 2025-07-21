<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class PromotionalPageController extends Controller
{
    public function showPromotionalPage()
    {
            // dd('I am inside the controller!');

        $products = Product::all();

        $reviews = Review::where('is_approved', true)
            ->latest()
            ->get();

        return view('index', [
            'products' => $products,
            'reviews' => $reviews,
        ]);
    }
}
