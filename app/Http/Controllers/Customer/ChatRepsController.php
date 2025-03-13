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
use Illuminate\Support\Facades\Crypt;

class ChatRepsController extends Controller
{
    public function index()
    {
        $authUserId = Auth::id();
        $superAdmins = SuperAdmin::select('id', 's_admin_username')->get();
        $admins = Admin::select('id', 'email')->get();
        $staff = Staff::select('id', 'email')->get();

        // Function to get the last message
        $getLastMessage = function ($id, $type) use ($authUserId) {
            return Conversation::where(function ($query) use ($id, $type, $authUserId) {
                $query->where('sender_id', $authUserId)
                      ->where('sender_type', 'customer')
                      ->where('receiver_id', $id)
                      ->where('receiver_type', $type);
            })->orWhere(function ($query) use ($id, $type, $authUserId) {
                $query->where('sender_id', $id)
                      ->where('sender_type', $type)
                      ->where('receiver_id', $authUserId)
                      ->where('receiver_type', 'customer');
            })->latest('created_at')->first();
        };

        // Function to count unread messages
        $countUnreadMessages = function ($id, $type) use ($authUserId) {
            return Conversation::where('sender_id', $id)
                ->where('sender_type', $type)
                ->where('receiver_id', $authUserId)
                ->where('receiver_type', 'customer')
                ->where('is_read', 0)
                ->count();
        };

        foreach ($superAdmins as $superadmin) {
            $superadmin->lastMessage = $getLastMessage($superadmin->id, 'super_admin');
            $superadmin->unreadCount = $countUnreadMessages($superadmin->id, 'super_admin');
        }

        foreach ($admins as $admin) {
            $admin->lastMessage = $getLastMessage($admin->id, 'admin');
            $admin->unreadCount = $countUnreadMessages($admin->id, 'admin');
        }

        foreach ($staff as $staffMember) {
            $staffMember->lastMessage = $getLastMessage($staffMember->id, 'staff');
            $staffMember->unreadCount = $countUnreadMessages($staffMember->id, 'staff');
        }

        $totalUnreadMessages = Conversation::where('receiver_id', $authUserId)
            ->where('receiver_type', 'customer')
            ->where('is_read', 0)
            ->count();

        return view('customer.chat', compact('superAdmins', 'admins', 'staff', 'totalUnreadMessages'));
    }

    public function show($id, $type)
    {
        if (!in_array($type, ['super_admin', 'admin', 'staff'])) {
            abort(403, 'Invalid chat recipient.');
        }

        $user = match ($type) {
            'super_admin' => SuperAdmin::findOrFail($id),
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            default => abort(404),
        };

        Conversation::markAsRead($id, $type, Auth::id(), 'customer');

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

        foreach ($conversations as $conversation) {
            $conversation->message = Crypt::decryptString($conversation->message);
        }

        return view('customer.chatting', compact('user', 'conversations', 'type'))
            ->with('receiverType', $type);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx|max:30000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        if (!$request->message && !$request->file('file')) {
            return redirect()->back()->with('error', 'Message or file is required.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => 'customer',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $encryptedMessage,
            'file_path' => $filePath,
            'is_read' => false,
        ]);

        return redirect()->back();
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|integer',
            'sender_type' => 'required|string',
        ]);

        $customerId = Auth::id();

        Conversation::where('sender_id', $request->sender_id)
            ->where('sender_type', $request->sender_type)
            ->where('receiver_id', $customerId)
            ->where('receiver_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}