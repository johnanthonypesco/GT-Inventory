<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupChatController extends Controller {
    public function index() {
        $conversations = GroupChat::with('sender')->get();
        return view('admin.GroupChat', compact('conversations'));
    }
    

    public function store(Request $request) {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048', // ✅ Accept images and docs
        ]);
    
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('group_chats', 'public'); // ✅ Store in `storage/app/public/group_chats`
        }
    
        GroupChat::create([
            'sender_id' => auth()->id(),
            'sender_type' => get_class(auth()->user()),
            'message' => $request->message,
            'file_path' => $filePath,
        ]);
    
        return back();
    }
    
}
