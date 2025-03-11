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
    $authUserId = Auth::id();

    foreach ($superAdmins as $admin) {
        $latestConversation = Conversation::where(function ($query) use ($authUserId, $admin) {
            $query->where('sender_id', $authUserId)
                  ->where('receiver_id', $admin->id);
        })->orWhere(function ($query) use ($authUserId, $admin) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $authUserId);
        })->orderBy('created_at', 'desc')->first();

        if ($latestConversation) {
            $admin->last_message = $latestConversation->message;
            $admin->last_file = $latestConversation->file_path;
            $admin->last_message_time = $latestConversation->created_at;
            $admin->last_sender_id = $latestConversation->sender_id;
        } else {
            $admin->last_message = null;
            $admin->last_file = null;
            $admin->last_message_time = null;
            $admin->last_sender_id = null;
        }
    }

    return view('customer.chat', compact('superAdmins', 'authUserId'));
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
    public function fetchNewMessages(Request $request, $last_id= null)
{
    // dd("ROUTE WORKS");
    $userId = Auth::id(); // Get the logged-in user ID

    // ✅ Fetch the latest message if no last_id is provided
    if ($last_id == 0) {
        $latestMessage = Conversation::where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestMessage) {
            return response()->json(['new_messages' => [], 'last_id' => 0]);
        }

        return response()->json([
            'new_messages' => [$latestMessage], // Return as an array
            'last_id' => $latestMessage->id
        ]);
    }

    // ✅ Fetch only new messages after `last_id`
    $newMessages = Conversation::where('id', '>', $last_id)
        ->where(function ($query) use ($userId) {
            $query->where('receiver_id', $userId)->orWhere('sender_id', $userId);
        })
        ->orderBy('id', 'asc')
        ->get();

    return response()->json([
        'new_messages' => $newMessages,
        'last_id' => $newMessages->isNotEmpty() ? $newMessages->last()->id : $last_id
    ]);
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
