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
}
