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
    public function showChat()
    {
        $authUser = Auth::user();
        $senderType = $this->getUserType($authUser);

        $superAdmins = collect();
        $admins = collect();
        $staff = collect();
        $customers = collect();

        switch ($senderType) {
            case 'super_admin':
                $superAdmins = SuperAdmin::where('id', '!=', $authUser->id)->get();
                $admins = Admin::all();
                $staff = Staff::where('id', '!=', $authUser->id)->get();
                $customers = User::all();
                break;

            case 'admin':
                $superAdmins = SuperAdmin::all(); // Admin can see Super Admins
                $admins = Admin::where('id', '!=', $authUser->id)->get();
                $staff = Staff::all();
                $customers = User::all();
                break;

            case 'staff':
                // ✅ BAGONG LOGIC PARA SA STAFF
                // Kukunin lang ang Super Admins at Admins na may conversation na sa staff.
                $superAdmins = SuperAdmin::all()->filter(function ($superAdmin) use ($authUser) {
                    return Conversation::where(function ($query) use ($superAdmin, $authUser) {
                        $query->where('sender_id', $superAdmin->id)->where('sender_type', 'super_admin')
                              ->where('receiver_id', $authUser->id)->where('receiver_type', 'staff');
                    })->orWhere(function ($query) use ($superAdmin, $authUser) {
                        $query->where('sender_id', $authUser->id)->where('sender_type', 'staff')
                              ->where('receiver_id', $superAdmin->id)->where('receiver_type', 'super_admin');
                    })->exists();
                });

                $admins = Admin::all()->filter(function ($admin) use ($authUser) {
                    return Conversation::where(function ($query) use ($admin, $authUser) {
                        $query->where('sender_id', $admin->id)->where('sender_type', 'admin')
                              ->where('receiver_id', $authUser->id)->where('receiver_type', 'staff');
                    })->orWhere(function ($query) use ($admin, $authUser) {
                        $query->where('sender_id', $authUser->id)->where('sender_type', 'staff')
                              ->where('receiver_id', $admin->id)->where('receiver_type', 'admin');
                    })->exists();
                });
                
                $locationId = $authUser->location_id;
                $customers = User::where('company_id', $locationId)->get();
                break;
            
            default:
                return redirect()->route('login');
        }

        $lastMessages = [];
        $unreadCounts = [];
        if ($senderType) {
            $allUsers = collect([])
                ->merge($superAdmins)
                ->merge($admins)
                ->merge($staff)
                ->merge($customers);

            foreach ($allUsers as $user) {
                $lastMessage = Conversation::where(function ($query) use ($user, $authUser, $senderType) {
                    $query->where('sender_id', $authUser->id)->where('sender_type', $senderType)
                          ->where('receiver_id', $user->id)->where('receiver_type', $this->getUserType($user));
                })->orWhere(function ($query) use ($user, $authUser, $senderType) {
                    $query->where('sender_id', $user->id)->where('sender_type', $this->getUserType($user))
                          ->where('receiver_id', $authUser->id)->where('receiver_type', $senderType);
                })->orderBy('created_at', 'desc')->first();

                if ($lastMessage) {
                    $senderName = $this->getSenderName($lastMessage);
                    $lastMessages[$user->id] = [
                        'sender_name' => $senderName,
                        'message' => $lastMessage->file_path ? 'Sent a file' : $lastMessage->message,
                        'time' => $lastMessage->created_at->format('h:i A'),
                    ];
                }

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

    // ... (Walang pagbabago sa ibang methods ng controller) ...

    private function getUserType($user)
    {
        if ($user instanceof SuperAdmin) { return 'super_admin'; } 
        elseif ($user instanceof Admin) { return 'admin'; } 
        elseif ($user instanceof Staff) { return 'staff'; } 
        elseif ($user instanceof User) { return 'customer'; }
        return null;
    }

    private function getSenderName($conversation)
    {
        $senderType = $conversation->sender_type;
        $senderId = $conversation->sender_id;
        switch ($senderType) {
            case 'super_admin':
                $sender = SuperAdmin::find($senderId);
                return $sender ? $sender->s_admin_username : 'Unknown';
            case 'admin':
                $sender = Admin::find($senderId);
                return $sender ? $sender->username : 'Unknown';
            case 'staff':
                $sender = Staff::find($senderId);
                return $sender ? $sender->staff_username : 'Unknown';
            case 'customer':
                $sender = User::find($senderId);
                return $sender ? $sender->name : 'Unknown';
            default:
                return 'Unknown';
        }
    }
    
    public function chatWithUser($id, $type)
    {
        if (!in_array($type, ['super_admin', 'admin', 'staff', 'customer'])) { abort(403, 'Invalid chat recipient.'); }
        $user = match ($type) {
            'super_admin' => SuperAdmin::findOrFail($id),
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };
        $authUser = Auth::user();
        if (!$authUser) { return redirect()->back()->with('error', 'Unauthorized'); }
        $senderType = $this->getUserType($authUser);
        Conversation::where('sender_id', $id)
            ->where('sender_type', $type)
            ->where('receiver_id', $authUser->id)
            ->where('receiver_type', $senderType)
            ->where('is_read', false)
            ->update(['is_read' => true]);
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
    
    public function fetchNewMessages(Request $request, $id, $type)
    {
        $lastMessageId = $request->query('last_id', 0);
        $authUser = Auth::user();
        $senderType = $this->getUserType($authUser);
        $newMessages = Conversation::where('id', '>', $lastMessageId)
            ->where(function ($query) use ($id, $type, $authUser, $senderType) {
                $query->where(function($q) use ($id, $type, $authUser, $senderType) {
                    $q->where('sender_id', $authUser->id)->where('sender_type', $senderType)
                      ->where('receiver_id', $id)->where('receiver_type', $type);
                })->orWhere(function($q) use ($id, $type, $authUser, $senderType) {
                    $q->where('sender_id', $id)->where('sender_type', $type)
                      ->where('receiver_id', $authUser->id)->where('receiver_type', $senderType);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
        $formattedMessages = $newMessages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'file_path' => $message->file_path ? url($message->file_path) : null,
                'created_at_formatted' => Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A'),
            ];
        });
        return response()->json($formattedMessages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4,mov,avi|max:30000',
        ]);
        $user = Auth::user();
        if (!$user) { return redirect()->back()->with('error', 'Unauthorized'); }
        $senderType = $this->getUserType($user);
        if ($senderType == 'customer' && $request->receiver_type == 'customer') { return redirect()->back()->with('error', 'Customers cannot message other customers.'); }
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->hashName();
            $subfolder = 'uploads/chat_files';
            if (App::environment('local')) {
                $targetDir = public_path($subfolder);
            } else {
                // This assumes your Laravel project is one level inside the root,
                // and public_html is the public directory at the root.
                $targetDir = base_path('../public_html/' . $subfolder);
            }
            if (!File::exists($targetDir)) { File::makeDirectory($targetDir, 0755, true, true); }
            $file->move($targetDir, $fileName);
            $filePath = $subfolder . '/' . $fileName;
        }
        if (!$request->message && !$filePath) { return redirect()->back()->with('error', 'Message or file is required.'); }
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