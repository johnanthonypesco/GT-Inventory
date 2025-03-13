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
use Illuminate\Support\Facades\Crypt;

class ChatController extends Controller
{
    /**
     * Show available users to chat with (Admins, Staff, SuperAdmins, Customers).
     */
    public function showChat()
    {
        $superAdmins = SuperAdmin::select('id', 's_admin_username')->get();
        $admins = Admin::select('id', 'username')->get();
        $staff = Staff::select('id', 'staff_username')->get();
        $customers = User::select('id', 'name')->get();

        return view('admin.chat', compact('superAdmins', 'admins', 'staff', 'customers'));
    }

    /**
     * Show the chat conversation between the logged-in user and a recipient.
     */
    public function chatWithUser($id, $type)
    {
        if (!in_array($type, ['super_admin', 'admin', 'staff', 'customer'])) {
            abort(403, 'Invalid chat recipient.');
        }

        $user = match ($type) {
            'super_admin' => SuperAdmin::findOrFail($id),
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };

        $authUser = Auth::user();
        if (!$authUser) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $senderType = match (get_class($authUser)) {
            'App\Models\SuperAdmin' => 'super_admin',
            'App\Models\Admin' => 'admin',
            'App\Models\Staff' => 'staff',
            'App\Models\User' => 'customer',
            default => abort(403, 'Invalid user type.'),
        };

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
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4,mov,avi|max:30000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $senderType = match (get_class($user)) {
            'App\Models\SuperAdmin' => 'super_admin',
            'App\Models\Admin' => 'admin',
            'App\Models\Staff' => 'staff',
            'App\Models\User' => 'customer',
            default => abort(403, 'Invalid user type.'),
        };

        if ($senderType == 'customer' && $request->receiver_type == 'customer') {
            return redirect()->back()->with('error', 'Customers cannot message other customers.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        if (!$request->message && !$filePath) {
            return redirect()->back()->with('error', 'Message or file is required.');
        }

        Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message ?: '',
            'file_path' => $filePath,
        ]);

        return redirect()->back();
    }
}