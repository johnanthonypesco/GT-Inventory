<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show available users to chat with (Admins, Staff, SuperAdmins).
     */
    public function showChat()
{
    $superAdmins = SuperAdmin::select('id', 's_admin_username')->get();
    $admins = Admin::select('id', 'username')->get();
    $staff = Staff::select('id', 'staff_username')->get();
    $customers = User::select('id', 'name')->get(); // Include Customers

    return view('admin.chat', compact('superAdmins', 'admins', 'staff', 'customers'));
}

    /**
     * Show the chat conversation between the logged-in user and a recipient.
     */
    public function chatWithUser($id, $type)
{
    // Validate the receiver type
    if (!in_array($type, ['super_admin', 'admin', 'staff', 'customer'])) {
        abort(403, 'Invalid chat recipient.');
    }

    // Fetch the user based on type
    $user = match ($type) {
        'super_admin' => SuperAdmin::findOrFail($id),
        'admin' => Admin::findOrFail($id),
        'staff' => Staff::findOrFail($id),
        'customer' => User::findOrFail($id),
        default => abort(404),
    };

    // Get authenticated user
    $authUser = Auth::user();
    if (!$authUser) {
        return redirect()->back()->with('error', 'Unauthorized');
    }

    // Determine sender type
    $senderType = match (get_class($authUser)) {
        'App\Models\SuperAdmin' => 'super_admin',
        'App\Models\Admin' => 'admin',
        'App\Models\Staff' => 'staff',
        'App\Models\User' => 'customer',
        default => abort(403, 'Invalid user type.'),
    };

    // Fetch chat messages
    $conversations = Conversation::where(function ($query) use ($id, $type, $authUser, $senderType) {
        $query->where('sender_id', $authUser->id)
              ->where('sender_type', $senderType)
              ->where('receiver_id', $id)
              ->where('receiver_type', $type);
    })->orWhere(function ($query) use ($id, $type, $authUser, $senderType) {
        $query->where('sender_id', $id)
              ->where('sender_type', $type)
              ->where('receiver_id', $authUser->id)
              ->where('receiver_type', $senderType);
    })->orderBy('created_at', 'asc')->get();

    // ✅ Fix: Pass receiverType correctly
    return view('admin.chatting', compact('user', 'conversations'))
        ->with('receiverType', $type);
}

    /**
     * Store a chat message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer', // ✅ Allow messaging customers
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);
    
        // Get authenticated user
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
    
        // Determine sender type
        $senderType = match (get_class($user)) {
            'App\Models\SuperAdmin' => 'super_admin',
            'App\Models\Admin' => 'admin',
            'App\Models\Staff' => 'staff',
            'App\Models\User' => 'customer',
            default => abort(403, 'Invalid user type.'),
        };
    
        // Prevent customer-to-customer messaging
        if ($senderType == 'customer' && $request->receiver_type == 'customer') {
            return redirect()->back()->with('error', 'Customers cannot message other customers.');
        }
    
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }
    
        // Ensure at least one of message or file is provided
        if (!$request->message && !$filePath) {
            return redirect()->back()->with('error', 'Message or file is required.');
        }
    
        // Store the message
        Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message ?? '',
            'file_path' => $filePath,
        ]);
    
        return redirect()->back();
    }
}    