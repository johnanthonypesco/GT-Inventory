<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageContents;

class ContentmanagementController extends Controller
{
    public function showContentmanagement()
    {
        // Fetch the content from the ManageContents model
        $content = ManageContents::all();
        return view('admin.contentmanagement', ['content' => $content]);
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

        return redirect()->route('admin.contentmanagement')->with('success', 'Content updated successfully.');
    }
}
