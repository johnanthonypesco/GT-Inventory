<?php

namespace App\Http\Controllers;

use App\Models\Product; // 1. Import the Product model
use App\Models\ManageContents;
use Illuminate\Http\Request;

class PromotionalPageController extends Controller
{
    public function showPromotionalPage()
    {
        // 2. Fetch all products from the database
        $products = Product::all();
        $content = ManageContents::all();

        // 3. Pass the $products variable to the view
        return view('index', ['products' => $products, 'content' => $content]);
    }
}