<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatRepsController extends Controller
{
    /**
     * Show available users (SuperAdmins, Admins, Staff) that the customer can chat with.
     */
    public function index()
    {
        $superAdmins = SuperAdmin::select('id', 's_admin_username')->get(); // Ensure column name matches database
        $admins = Admin::select('id', 'email')->get(); // Ensure column exists
        $staff = Staff::select('id', 'email')->get(); // Ensure column exists
        return view('customer.chat', compact('superAdmins', 'admins', 'staff'));
    }
    
    /**
     * Show the chat conversation between the logged-in user and a recipient.
     */
    public function show($id, $type)
    {
        // Ensure the receiver type is valid (SuperAdmins, Admins, Staff)
        if (!in_array($type, ['super_admin', 'admin', 'staff'])) {
            abort(403, 'Invalid chat recipient.');
        }

        // Fetch the recipient based on type
        $user = match ($type) {
            'super_admin' => SuperAdmin::findOrFail($id),
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            default => abort(404),
        };

        // Fetch chat messages between the authenticated user and the recipient
        $conversations = Conversation::where(function ($query) use ($id, $type) {
            $query->where('sender_id', Auth::id())
                  ->where('sender_type', 'customer')
                  ->where('receiver_id', $id)
                  ->where('receiver_type', $type);
        })->orWhere(function ($query) use ($id, $type) {
            $query->where('sender_id', $id)
                  ->where('sender_type', $type)
                  ->where('receiver_id', Auth::id())
                  ->where('receiver_type', 'customer');
        })->orderBy('created_at', 'asc')->get();

        return view('customer.chatting', compact('user', 'conversations', 'type'))
        ->with('receiverType', $type);
    }

    /**
     * Fetch new messages dynamically (for real-time chat updates).
     */
    public function fetchNewMessages(Request $request)
    {
        $lastId = $request->query('last_id', 0);
        $receiverId = $request->query('receiver_id');
        $receiverType = $request->query('receiver_type');

        if (!$receiverId || !$receiverType) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        $newMessages = Conversation::where('id', '>', $lastId)
            ->where(function ($query) use ($receiverId, $receiverType) {
                $query->where('sender_id', Auth::id())
                      ->where('sender_type', 'customer')
                      ->where('receiver_id', $receiverId)
                      ->where('receiver_type', $receiverType)
                      ->orWhere('sender_id', $receiverId)
                      ->where('sender_type', $receiverType)
                      ->where('receiver_id', Auth::id())
                      ->where('receiver_type', 'customer');
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($newMessages);
    }

    /**
     * Store a chat message (text or file).
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Get authenticated user
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        // Ensure at least one of message or file is provided
        if (!$request->message && !$request->file('file')) {
            return redirect()->back()->with('error', 'Message or file is required.');
        }

        // Handle file upload (if any)
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        // Store the chat message
        Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => 'customer',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'file_path' => $filePath,
        ]);

        return redirect()->back();
    }

    /**
     * Convert URLs in text into clickable links.
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
