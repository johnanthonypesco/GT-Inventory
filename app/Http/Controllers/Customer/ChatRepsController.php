<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatRepsController extends Controller
{
    public function index()
    {
        $superAdmins = SuperAdmin::all();
        return view('customer.chat', compact('superAdmins'));
    }

    public function show($id)
    {
        $user = SuperAdmin::findOrFail($id);
        $superAdmin = SuperAdmin::first(); // ✅ Ensure SuperAdmin exists
    
        if (!$superAdmin) {
            return back()->with('error', 'SuperAdmin not found.');
        }
    
        $conversations = Conversation::where(function ($query) use ($id) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('sender_id', $id)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        // ✅ Convert message text to clickable links
        foreach ($conversations as $message) {
            $message->message = nl2br($this->makeClickableLinks($message->message));
        }
    
        return view('customer.chatting', compact('user', 'conversations', 'superAdmin'));
    }

    public function fetchNewMessages(Request $request)
    {
        $lastId = $request->query('last_id', 0);

        // ✅ Get the SuperAdmin ID (Assuming Only One SuperAdmin)
        $superAdmin = SuperAdmin::first(); 
        if (!$superAdmin) {
            return response()->json(['error' => 'SuperAdmin not found'], 404);
        }
        $superAdminId = $superAdmin->id;

        $newMessages = Conversation::where('id', '>', $lastId)
            ->where(function ($query) use ($superAdminId) {
                $query->where('sender_id', Auth::id())->where('receiver_id', $superAdminId)
                      ->orWhere('sender_id', $superAdminId)->where('receiver_id', Auth::id());
            })
            ->orderBy('id')
            ->get();

        // ✅ Convert message text to clickable links
        foreach ($newMessages as $message) {
            $message->message = nl2br($this->makeClickableLinks($message->message));
        }

        return response()->json($newMessages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:1024000'
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        if (!$request->message && !$filePath) {
            return back()->with('error', 'Message or file is required.');
        }

        $receiver = SuperAdmin::find($request->receiver_id);
        if (!$receiver) {
            return back()->with('error', 'Receiver not found.');
        }

        Conversation::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'customer',
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'file_path' => $filePath
        ]);

        return back();
    }

    /**
     * ✅ Convert URLs in text into clickable links.
     */
    private function makeClickableLinks($text)
    {
        return preg_replace(
            '/(https?:\/\/[^\s]+)/', 
            '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>', 
            e($text)
        );
    }
}
