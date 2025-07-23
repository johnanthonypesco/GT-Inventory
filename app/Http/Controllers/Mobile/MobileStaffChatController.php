<?php

namespace App\Http\Controllers\mobile;

use App\Models\User;
use App\Models\Staff;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;  // Add this
use Illuminate\Support\Facades\File; // Add this

class MobileStaffChatController extends Controller
{
    /**
     * Get the list of conversations for the logged-in staff member.
     */
    public function getConversations(Request $request)
    {
        $staffUser = $request->user();
        $conversations = collect();

        if ($staffUser->location_id) {
            $customers = User::where('company_id', $staffUser->location_id)->get();
            $conversations = $this->formatContactList($customers, $staffUser, 'staff');
        }

        return response()->json($conversations);
    }

    /**
     * Get the full message history with a specific user.
     */
    public function getMessages(Request $request, $id, $type)
    {
        $authUser = $request->user();
        $authType = 'staff';

        // Mark messages as read
        Conversation::where('sender_id', $id)
            ->where('sender_type', $type)
            ->where('receiver_id', $authUser->id)
            ->where('receiver_type', $authType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch all conversations
        $messages = Conversation::where(function ($query) use ($id, $type, $authUser, $authType) {
            $query->where('sender_id', $authUser->id)
                  ->where('sender_type', $authType)
                  ->where('receiver_id', $id)
                  ->where('receiver_type', $type);
        })->orWhere(function ($query) use ($id, $type, $authUser, $authType) {
            $query->where('sender_id', $id)
                  ->where('sender_type', $type)
                  ->where('receiver_id', $authUser->id)
                  ->where('receiver_type', $authType);
        })->orderBy('created_at', 'asc')->get();

        // Format for mobile app
        $formattedMessages = $messages->map(function ($message) use ($authUser) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                // ✅ Use the url() helper to generate the correct full URL
                'file_path' => $message->file_path ? url($message->file_path) : null,
                'created_at' => $message->created_at,
                'is_sender' => $message->sender_id == $authUser->id && $message->sender_type == 'staff',
            ];
        });

        return response()->json($formattedMessages);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:customer,admin,super_admin',
            'message' => 'required_without:file|nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4,mov,avi|max:30000',
        ]);

        $sender = $request->user();
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // ✅ Use hashName for a unique, safe filename
            $fileName = $file->hashName();
            $subfolder = 'uploads/chat_files';

            // ✅ Determine target directory based on environment
            $targetDir = App::environment('local')
                ? public_path($subfolder)
                : base_path('../public_html/' . $subfolder);

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            // Move the file to the target directory
            $file->move($targetDir, $fileName);

            // ✅ Store the relative path in the database
            $filePath = $subfolder . '/' . $fileName;
        }

        $conversation = Conversation::create([
            'sender_id' => $sender->id,
            'sender_type' => 'staff',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message ?: '',
            'file_path' => $filePath,
        ]);

        return response()->json([
            'id' => $conversation->id,
            'message' => $conversation->message,
            // ✅ Use url() to return the full path to the frontend
            'file_path' => $conversation->file_path ? url($conversation->file_path) : null,
            'created_at' => $conversation->created_at,
            'is_sender' => true,
        ], 201);
    }

    /**
     * Helper to format the list of contacts.
     */
    private function formatContactList($users, $authUser, $authType)
    {
        // This collection will hold contacts with their message data
        $contacts = $users->map(function ($user) use ($authUser, $authType) {
            $userType = 'customer';

            $lastMessage = Conversation::where(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $authUser->id)->where('sender_type', $authType)
                      ->where('receiver_id', $user->id)->where('receiver_type', $userType);
            })->orWhere(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $user->id)->where('sender_type', $userType)
                      ->where('receiver_id', $authUser->id)->where('receiver_type', $authType);
            })->orderBy('created_at', 'desc')->first();

            $unreadCount = Conversation::where('sender_id', $user->id)->where('sender_type', $userType)
                ->where('receiver_id', $authUser->id)->where('receiver_type', $authType)
                ->where('is_read', false)
                ->count();

            return (object)[ // Return as an object for easier property access
                'id' => $user->id,
                'name' => $user->name,
                'type' => $userType,
                // ✅ Better display text for last message
                'last_message_text' => $lastMessage ? ($lastMessage->file_path ? 'Sent a file' : $lastMessage->message) : 'No messages yet.',
                'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => $unreadCount,
            ];
        });

        // ✅ Sort reliably by the actual timestamp and then format the time for display
        return $contacts->sortByDesc('last_message_time')
            ->map(function ($contact) {
                $contact->last_message_time = $contact->last_message_time ? $contact->last_message_time->diffForHumans() : '';
                return (array)$contact; // Convert back to array for JSON response
            })->values();
    }
}