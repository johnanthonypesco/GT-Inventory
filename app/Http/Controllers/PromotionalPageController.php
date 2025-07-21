<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\ManageContents;
use Illuminate\Http\Request;

class PromotionalPageController extends Controller
{
    public function showPromotionalPage()
    {
        // 2. Fetch all products from the database
        $products = Product::all();
        $content = ManageContents::all();

        $reviews = Review::where('is_approved', true)
            ->latest()
            ->get();

        return view('index', ['products' => $products, 'reviews' => $reviews ,'content' => $content]);
    }
}
