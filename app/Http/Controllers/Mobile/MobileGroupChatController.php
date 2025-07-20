<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MobileGroupChatController extends Controller
{
    /**
     * Fetch all group chat messages for the mobile app.
     */
    public function getGroupMessages(Request $request)
    {
        $authUser = $request->user();
        $messages = GroupChat::with('sender')->orderBy('created_at', 'asc')->get();

        $formattedMessages = $messages->map(function ($message) use ($authUser) {
            // Decrypt the message content before sending
            try {
                $decryptedMessage = $message->message ? Crypt::decryptString($message->message) : '';
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $decryptedMessage = '[Message could not be decrypted]';
            }
            
            // Determine the sender's display name
            $senderName = 'Unknown User';
            if ($message->sender) {
                // You can customize this based on the sender model (Staff, Admin, etc.)
                $senderName = $message->sender->staff_username ?? $message->sender->name ?? 'User';
            }

            return [
                'id' => $message->id,
                'message' => $decryptedMessage,
                'file_path' => $message->file_path ? Storage::url($message->file_path) : null,
                'created_at' => $message->created_at,
                'is_sender' => $message->sender_id == $authUser->id && get_class($message->sender) == get_class($authUser),
                'sender_name' => $senderName,
            ];
        });

        return response()->json($formattedMessages);
    }

    /**
     * Store a new group chat message from the mobile app.
     */
    public function sendGroupMessage(Request $request)
    {
        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:20480',
        ]);

        $sender = $request->user();
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('group_chats', 'public');
        }

        $message = GroupChat::create([
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'message' => $request->message ? Crypt::encryptString($request->message) : null,
            'file_path' => $filePath,
        ]);

        // Return the newly created message, formatted for the app
        return response()->json([
            'id' => $message->id,
            'message' => $request->message, // Return plain text, no need to re-decrypt
            'file_path' => $filePath ? Storage::url($filePath) : null,
            'created_at' => $message->created_at,
            'is_sender' => true,
            'sender_name' => $sender->staff_username ?? $sender->name,
        ], 201);
    }
}