<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageContents;
use App\Models\Product;

//history log
use App\Http\Controllers\Admin\HistorylogController;

class ContentmanagementController extends Controller
{
    public function showContentmanagement()
    {
        $content = ManageContents::all();
        $product = Product::all();
        $enabledProducts = Product::where('is_displayed', 1)->get(); 

        return view('admin.contentmanagement', [
            'content' => $content,
            'product' => $product,
            'products' => $enabledProducts
        ]);
    }

    public function enabledisable($id)
    {
        $product = Product::findOrFail($id);
        $product->is_displayed = !$product->is_displayed;
        $product->save();

        return redirect()->back()->with('status', 'Product status updated!');
    }


    public function editContent(Request $request, $id)
    {
        //add validation rules
        $request->validate([
            'aboutus1' => 'required|string|max:255',
            'aboutus2' => 'required|string|max:255',
            'aboutus3' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
        ]);

        $content = ManageContents::findOrFail($id);

        $content->aboutus1 = $request->input('aboutus1');
        $content->aboutus2 = $request->input('aboutus2');
        $content->aboutus3 = $request->input('aboutus3');
        $content->contact_number = $request->input('contact_number');
        $content->email = $request->input('email');
        $content->address = $request->input('address');

        $content->save();
        // Log the content update
        HistorylogController::addproductlog('Edit', 'Content ' . $id . ' has been updated by ');
        return redirect()->route('admin.contentmanagement')->with('success', 'Content updated successfully.');
    }
}
