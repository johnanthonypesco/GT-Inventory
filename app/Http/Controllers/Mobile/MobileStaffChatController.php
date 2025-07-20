<?php

namespace App\Http\Controllers\mobile;

use App\Models\User;
use App\Models\Staff;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MobileStaffChatController extends Controller
{
    /**
     * Get the list of conversations for the logged-in staff member.
     * For staff, this will primarily be customers from their assigned location.
     */
    public function getConversations(Request $request)
    {
        $staffUser = $request->user(); // Authenticated staff user
        $conversations = collect();

        // Staff can only see Customers from their assigned location
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

        // Fetch all conversations between the two users
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
                'file_path' => $message->file_path,
                'created_at' => $message->created_at,
                // Add a flag to easily identify if the message is from the logged-in user
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
            'receiver_type' => 'required|string|in:customer,admin,super_admin', // Staff can message these roles
            'message' => 'required_without:file|nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4,mov,avi|max:30000',
        ]);

        $sender = $request->user();
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/chat_files'), $fileName);
            $filePath = asset("uploads/chat_files/{$fileName}");
        }

        $conversation = Conversation::create([
            'sender_id' => $sender->id,
            'sender_type' => 'staff',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message ?: '',
            'file_path' => $filePath,
        ]);

        // Return the newly created message so the frontend can display it instantly
        return response()->json([
            'id' => $conversation->id,
            'message' => $conversation->message,
            'file_path' => $conversation->file_path,
            'created_at' => $conversation->created_at,
            'is_sender' => true,
        ], 201);
    }

    /**
     * Helper to format the list of contacts with last message and unread count.
     */
    private function formatContactList($users, $authUser, $authType)
    {
        return $users->map(function ($user) use ($authUser, $authType) {
            $userType = 'customer'; // In this controller, staff only message customers

            // Get last message
            $lastMessage = Conversation::where(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $authUser->id)->where('sender_type', $authType)
                      ->where('receiver_id', $user->id)->where('receiver_type', $userType);
            })->orWhere(function ($query) use ($user, $userType, $authUser, $authType) {
                $query->where('sender_id', $user->id)->where('sender_type', $userType)
                      ->where('receiver_id', $authUser->id)->where('receiver_type', $authType);
            })->orderBy('created_at', 'desc')->first();

            // Get unread count
            $unreadCount = Conversation::where('sender_id', $user->id)->where('sender_type', $userType)
                ->where('receiver_id', $authUser->id)->where('receiver_type', $authType)
                ->where('is_read', false)
                ->count();
            
            return [
                'id' => $user->id,
                'name' => $user->name, // Customer name
                'type' => $userType,
                'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet.',
                'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc('last_message_time');
    }
}