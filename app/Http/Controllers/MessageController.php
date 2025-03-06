<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model

class MessageController extends Controller
{
    public function chat()
    {
    // dd('chatis called');
        // $userId = Auth::id();
        
        // if (!$userId) {
        //     abort(403, 'User is not authenticated!');
        // }
    
        // \Log::info("Chat method is called by user ID: " . $userId);
    
        // $contacts = Admin::where('id', '!=', $userId)->get();
        
        // return view('admin.chat', compact('contacts'));
        

        
    }
    
    
    
    

    
}
