<?php

namespace App\Http\Controllers\mobile;

use App\Models\User;
use App\Models\Staff;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use App\Models\GroupMessage; // Assuming you have a model for group messages
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class MobileStaffChatController extends Controller
{
    /**
     * Get the list of direct message conversations for the logged-in staff member.
     */
    public function getConversations(Request $request)
    {
        $staffUser = $request->user();
        $conversations = collect();

        if ($staffUser->location_id) {
            // 1. Get all relevant user types
            $customers = User::where('company_id', $staffUser->location_id)->get();
            $superAdmins = SuperAdmin::active()->get();
            $admins = Admin::active()->get();

            // 2. Merge all collections into a single list
            $allUsers = $customers->concat($superAdmins)->concat($admins);

            // 3. Format the list for the mobile app
            $conversations = $this->formatContactList($allUsers, $staffUser, 'staff');
        }

        return response()->json($conversations);
    }

    /**
     * Get the messages for a specific direct conversation.
     * This method also marks the messages as read.
     */
    public function getMessages(Request $request, $id, $type)
    {
        $authUser = $request->user();
        $authType = 'staff';

        // Automatically mark incoming messages from this user as read
        Conversation::where('sender_id', $id)
            ->where('sender_type', $type)
            ->where('receiver_id', $authUser->id)
            ->where('receiver_type', $authType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch all messages for the conversation
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

        // Format messages for the mobile app
        $formattedMessages = $messages->map(function ($message) use ($authUser) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'file_path' => $message->file_path ? url($message->file_path) : null,
                'created_at' => $message->created_at,
                'is_sender' => $message->sender_id == $authUser->id && $message->sender_type == 'staff',
            ];
        });

        return response()->json($formattedMessages);
    }

    /**
     * Send a new direct message.
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
            $fileName = $file->hashName();
            $subfolder = 'uploads/chat_files';

            $targetDir = App::environment('local')
                ? public_path($subfolder)
                : base_path('../public_html/' . $subfolder);

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $file->move($targetDir, $fileName);
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
            'file_path' => $conversation->file_path ? url($conversation->file_path) : null,
            'created_at' => $conversation->created_at,
            'is_sender' => true,
        ], 201);
    }

    /**
     * Get all messages from the group chat.
     */
    public function getGroupMessages(Request $request)
    {
        $authUser = $request->user();
        $messages = GroupMessage::with('sender')->orderBy('created_at', 'asc')->get();

        $formattedMessages = $messages->map(function ($message) use ($authUser) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'file_path' => $message->file_path ? url($message->file_path) : null,
                'created_at' => $message->created_at,
                'is_sender' => $message->sender_id == $authUser->id && $message->sender_type == 'staff',
                'sender_name' => $message->sender->name ?? 'Unknown User', // Requires a 'sender' relationship on GroupMessage model
            ];
        });

        return response()->json($formattedMessages);
    }

    /**
     * Send a new message to the group chat.
     */
    public function sendGroupMessage(Request $request)
    {
        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file' => 'nullable|file|max:30000',
        ]);

        $sender = $request->user();
        $filePath = null;

        if ($request->hasFile('file')) {
             // Re-using the same file upload logic
            $file = $request->file('file');
            $fileName = $file->hashName();
            $subfolder = 'uploads/chat_files/group'; // Separate subfolder for group files

            $targetDir = App::environment('local')
                ? public_path($subfolder)
                : base_path('../public_html/' . $subfolder);

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }
            $file->move($targetDir, $fileName);
            $filePath = $subfolder . '/' . $fileName;
        }
        
        // Assuming you have a GroupMessage model
        $groupMessage = GroupMessage::create([
            'sender_id' => $sender->id,
            'sender_type' => 'staff',
            'message' => $request->message ?: '',
            'file_path' => $filePath,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Group message sent.'], 201);
    }


    /**
     * Helper method to format the list of contacts for the mobile app.
     */
    private function formatContactList($users, $authUser, $authType)
    {
        $contacts = $users->map(function ($user) use ($authUser, $authType) {
            
            // Dynamically determine the user type based on the model instance
            $userType = '';
            if ($user instanceof \App\Models\User) {
                $userType = 'customer';
            } elseif ($user instanceof \App\Models\Admin) {
                $userType = 'admin';
            } elseif ($user instanceof \App\Models\SuperAdmin) {
                $userType = 'super_admin';
            } else {
                return null; // Skip any unknown user types
            }

            // Get the last message in the conversation
            $lastMessage = Conversation::where(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $authUser->id)->where('sender_type', $authType)
                      ->where('receiver_id', $user->id)->where('receiver_type', $userType);
            })->orWhere(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $user->id)->where('sender_type', $userType)
                      ->where('receiver_id', $authUser->id)->where('receiver_type', $authType);
            })->orderBy('created_at', 'desc')->first();

            // Count unread messages from this user
            $unreadCount = Conversation::where('sender_id', $user->id)->where('sender_type', $userType)
                ->where('receiver_id', $authUser->id)->where('receiver_type', $authType)
                ->where('is_read', false)
                ->count();

            return (object)[ 
                'id' => $user->id,
                'name' => $user->name, // Relies on the getNameAttribute accessor in each model
                'type' => $userType,
                'last_message_text' => $lastMessage ? ($lastMessage->file_path ? 'Sent a file' : $lastMessage->message) : 'No messages yet.',
                'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => $unreadCount,
            ];
        })->filter(); // Use filter() to remove any null values from the map

        // Sort contacts to show the most recent conversations first
        return $contacts->sortByDesc('last_message_time')
            ->map(function ($contact) {
                $contact->last_message_time = $contact->last_message_time ? $contact->last_message_time->diffForHumans() : '';
                return (array)$contact;
            })->values();
    }
    public function getUnreadCount(Request $request)
    {
        $authUser = $request->user();
        $authType = 'staff';

        $count = Conversation::where('receiver_id', $authUser->id)
                             ->where('receiver_type', $authType)
                             ->where('is_read', false)
                             ->count();

        return response()->json(['unread_count' => $count]);
    }
}