<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\App; // Add this
use Illuminate\Support\Facades\File; // Add this

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
                // ✅ Use the url() helper for correct path generation
                'file_path' => $message->file_path ? url($message->file_path) : null,
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
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:20480', // max 20MB
        ]);

        $sender = $request->user();
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->hashName(); // Use hashName for unique, safe filenames
            $subfolder = 'group_chats';

            // ✅ Determine target directory based on environment
            if (App::environment('local')) {
                // Localhost: use public_path() which points to the 'public' folder
                $targetDir = public_path($subfolder);
            } else {
                // Production (Hostinger): construct path to 'public_html'
                $targetDir = base_path('../public_html/' . $subfolder);
            }

            // Create directory if it doesn't exist
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            // Move the file to the target directory
            $file->move($targetDir, $fileName);

            // Path to be saved in the database (relative public path)
            $filePath = $subfolder . '/' . $fileName;
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
             // ✅ Use the url() helper for correct path generation
            'file_path' => $filePath ? url($filePath) : null,
            'created_at' => $message->created_at,
            'is_sender' => true,
            'sender_name' => $sender->staff_username ?? $sender->name,
        ], 201);
    }
}