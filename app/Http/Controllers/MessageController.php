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
        $userId = Auth::id();
        
        if (!$userId) {
            abort(403, 'User is not authenticated!');
        }
    
        \Log::info("Chat method is called by user ID: " . $userId);
    
        $contacts = Admin::where('id', '!=', $userId)->get();
        
        return view('admin.chat', compact('contacts'));
    }
    
    
    
    public function fetchMessages($receiver_id)
    {
        $messages = Message::where(function ($query) use ($receiver_id) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
