<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MobileChatController extends Controller
{
    public function getConversations()
{
    $authUserId = Auth::id();

    $superAdmins = SuperAdmin::select('id', 's_admin_username as email')->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'email' => $item->email,
            'type' => 'super_admin'
        ];
    });

    $admins = Admin::select('id', 'email')->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'email' => $item->email,
            'type' => 'admin'
        ];
    });

    $staff = Staff::select('id', 'email')->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'email' => $item->email,
            'type' => 'staff'
        ];
    });

    $contacts = $superAdmins->merge($admins)->merge($staff);

    $contacts->transform(function ($contact) use ($authUserId) {
        $contact['lastMessage'] = Conversation::where(function ($query) use ($contact, $authUserId) {
            $query->where('sender_id', $authUserId)
                ->where('receiver_id', $contact['id'])
                ->where('receiver_type', $contact['type']);
        })->orWhere(function ($query) use ($contact, $authUserId) {
            $query->where('sender_id', $contact['id'])
                ->where('receiver_id', $authUserId)
                ->where('sender_type', $contact['type']);
        })->latest('created_at')->first();

        $contact['unreadCount'] = Conversation::where('sender_id', $contact['id'])
            ->where('receiver_id', $authUserId)
            ->where('sender_type', $contact['type'])
            ->where('is_read', 0)
            ->count();

        return $contact;
    });

    return response()->json([
        'success' => true,
        'contacts' => $contacts
    ]);
}

public function getMessages($id, $type)
{
    $authUserId = Auth::id();

    $conversations = Conversation::where(function ($query) use ($id, $authUserId, $type) {
        $query->where('sender_id', $authUserId)
            ->where('receiver_id', $id)
            ->where('receiver_type', $type);
    })->orWhere(function ($query) use ($id, $authUserId, $type) {
        $query->where('sender_id', $id)
            ->where('receiver_id', $authUserId)
            ->where('sender_type', $type);
    })->orderBy('created_at', 'asc')->get();

    foreach ($conversations as $conversation) {
        // ✅ Check if the message exists and decrypt it properly
        if (!empty($conversation->message)) {
            try {
                // $conversation->message = Crypt::decryptString($conversation->message);
            } catch (\Exception $e) {
                // In case of an error, return raw data instead of breaking the chat
                $conversation->message = "[Error: Message could not be decrypted]";
            }
        }
    }

    return response()->json([
        'success' => true,
        'messages' => $conversations
    ]);
}


public function sendMessage(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|integer',
        'receiver_type' => 'required|string',
        'message' => 'nullable|string',
        'file' => 'nullable|file|max:30000'
    ]);

    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    if (!$request->message && !$request->file('file')) {
        return response()->json(['error' => 'Message or file is required'], 400);
    }

    $filePath = null;
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('chat_files', 'public');
    }

    // $encryptedMessage = $request->message ? Crypt::encryptString($request->message) : null;

    $message = Conversation::create([
        'sender_id' => $user->id,
        'sender_type' => 'customer',
        'receiver_id' => $request->receiver_id,
        'receiver_type' => $request->receiver_type,
        'message' => $request->message,
        'file_path' => $filePath,
        'is_read' => false,
    ]);

    return response()->json(['success' => true, 'message' => $message]);
}
}