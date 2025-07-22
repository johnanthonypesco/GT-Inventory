<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ChatController extends Controller
{
    /**
     * Show available users to chat with (Admins, Staff, SuperAdmins, Customers).
     */
    public function showChat()
{
    $authUser = Auth::user();
    $senderType = match (get_class($authUser)) {
        'App\Models\SuperAdmin' => 'super_admin',
        'App\Models\Admin' => 'admin',
        'App\Models\Staff' => 'staff',
        'App\Models\User' => 'customer',
        default => null,
    };

    // Initialize variables
    $superAdmins = collect();
    $admins = collect();
    $staff = collect();
    $customers = collect();

    // Fetch contacts based on the logged-in user's role
    switch ($senderType) {
        case 'super_admin':
            // Super Admin can see all contacts
            $superAdmins = SuperAdmin::where('id', '!=', $authUser->id)->get();
            $admins = Admin::all();
            $staff = Staff::all();
            $customers = User::all();
            break;

        case 'admin':
            // Admin can see Staff and Customers, but not Super Admins
            $admins = Admin::where('id', '!=', $authUser->id)->get();
            $staff = Staff::all();
            $customers = User::all();
            break;

        case 'staff':
            // Staff can only see Customers with company_id = location_id of the staff
            $locationId = $authUser->location_id; // Get the staff's location_id
            $customers = User::where('company_id', $locationId)->get(); // Filter customers
            break;

        case 'customer':
            // Customers can only see Admins and Staff
            $admins = Admin::all();
            $staff = Staff::all();
            break;

        default:
            // Default case (e.g., unauthenticated)
            return redirect()->route('login');
    }

    // Get the last message and unread count for each user
    $lastMessages = [];
    $unreadCounts = [];
    if ($senderType) {
        $allUsers = collect([])
            ->merge($superAdmins)
            ->merge($admins)
            ->merge($staff)
            ->merge($customers);

        foreach ($allUsers as $user) {
            // Get the latest message between the logged-in user and the current user
            $lastMessage = Conversation::where(function ($query) use ($user, $authUser, $senderType) {
                $query->where('sender_id', $authUser->id)
                    ->where('sender_type', $senderType)
                    ->where('receiver_id', $user->id)
                    ->where('receiver_type', $this->getUserType($user));
            })->orWhere(function ($query) use ($user, $authUser, $senderType) {
                $query->where('sender_id', $user->id)
                    ->where('sender_type', $this->getUserType($user))
                    ->where('receiver_id', $authUser->id)
                    ->where('receiver_type', $senderType);
            })->orderBy('created_at', 'desc')->first();

            if ($lastMessage) {
                // Get the sender's name and role
                $senderName = $this->getSenderName($lastMessage);
                $lastMessages[$user->id] = [
                    'sender_name' => $senderName,
                    'message' => $lastMessage->message,
                    'time' => $lastMessage->created_at->format('h:i A'),
                ];
            }

            // Get the unread message count for the current user
            $unreadCounts[$user->id] = Conversation::where('receiver_id', $authUser->id)
                ->where('receiver_type', $senderType)
                ->where('sender_id', $user->id)
                ->where('sender_type', $this->getUserType($user))
                ->where('is_read', false)
                ->count();
        }
    }

    return view('admin.chat', compact('superAdmins', 'admins', 'staff', 'customers', 'lastMessages', 'unreadCounts'));
}

    private function getUserType($user)
    {
        if ($user instanceof SuperAdmin) {
            return 'super_admin';
        } elseif ($user instanceof Admin) {
            return 'admin';
        } elseif ($user instanceof Staff) {
            return 'staff';
        } elseif ($user instanceof User) {
            return 'customer';
        }
        return null;
    }

    private function getSenderName($conversation)
    {
        $senderType = $conversation->sender_type;
        $senderId = $conversation->sender_id;

        switch ($senderType) {
            case 'super_admin':
                $sender = SuperAdmin::find($senderId);
                return $sender ? $sender->s_admin_username . ' (Super Admin)' : 'Unknown Super Admin';
            case 'admin':
                $sender = Admin::find($senderId);
                return $sender ? $sender->username . ' (Admin)' : 'Unknown Admin';
            case 'staff':
                $sender = Staff::find($senderId);
                return $sender ? $sender->staff_username . ' (Staff)' : 'Unknown Staff';
            case 'customer':
                $sender = User::find($senderId);
                return $sender ? $sender->name . ' (Customer)' : 'Unknown Customer';
            default:
                return 'Unknown Sender';
        }
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

        // Mark messages as read when the chat is opened
        Conversation::where('sender_id', $id)
            ->where('sender_type', $type)
            ->where('receiver_id', $authUser->id)
            ->where('receiver_type', $senderType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch all conversations
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

        // ✅ Transform file_path from relative path to full URL for the view
        $conversations->transform(function ($conversation) {
            if ($conversation->file_path) {
                // Check if it's not already a full URL before applying url()
                if (!filter_var($conversation->file_path, FILTER_VALIDATE_URL)) {
                     $conversation->file_path = url($conversation->file_path);
                }
            }
            return $conversation;
        });

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
            $file = $request->file('file');
            // ✅ Use hashName for a unique, safe filename
            $fileName = $file->hashName();
            $subfolder = 'uploads/chat_files';

            // ✅ Determine target directory based on environment
            if (App::environment('local')) {
                $targetDir = public_path($subfolder);
            } else {
                $targetDir = base_path('../public_html/' . $subfolder);
            }

            // Create directory if it doesn't exist
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            // Move the file to the target directory
            $file->move($targetDir, $fileName);

            // ✅ Store the relative path in the database
            $filePath = $subfolder . '/' . $fileName;
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