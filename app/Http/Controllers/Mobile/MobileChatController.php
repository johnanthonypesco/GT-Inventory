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
use Illuminate\Support\Facades\App; // Add this
use Illuminate\Support\Facades\File; // Add this


class MobileChatController extends Controller
{
    // ... (getConversations and getMessages methods remain the same) ...
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
            if (!empty($conversation->message)) {
                try {
                    // Decryption appears to be intentionally commented out.
                    // $conversation->message = Crypt::decryptString($conversation->message);
                } catch (\Exception $e) {
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
            'file' => 'nullable|file|max:30000' // max 30MB
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
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $subfolder = 'chat_files';

            // Determine target directory based on environment
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

            // Path to be saved in the database (relative path)
            $filePath = $subfolder . '/' . $fileName;
        }

        // Encryption appears to be intentionally commented out.
        // $encryptedMessage = $request->message ? Crypt::encryptString($request->message) : null;

        $message = Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => 'customer',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message, // Storing plain text as per your original code
            'file_path' => $filePath,
            'is_read' => false,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}