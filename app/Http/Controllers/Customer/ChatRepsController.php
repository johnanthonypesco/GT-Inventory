<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ChatRepsController extends Controller
{
    public function index()
    {
        $authUserId = Auth::id();
        $customer = User::find($authUserId);

        if (!$customer || !$customer->company) {
            abort(403, 'Customer does not belong to a company.');
        }
        $customerCompanyId = $customer->company->id;

        $superAdmins = SuperAdmin::all();
        $admins = Admin::all();
        $staff = Staff::whereHas('location', function ($query) use ($customerCompanyId) {
            $query->where('id', $customerCompanyId);
        })->get();

        $getLastMessage = function ($id, $type) use ($authUserId) {
            return Conversation::where(function ($query) use ($id, $type, $authUserId) {
                $query->where('sender_id', $authUserId)->where('sender_type', 'customer')
                      ->where('receiver_id', $id)->where('receiver_type', $type);
            })->orWhere(function ($query) use ($id, $type, $authUserId) {
                $query->where('sender_id', $id)->where('sender_type', $type)
                      ->where('receiver_id', $authUserId)->where('receiver_type', 'customer');
            })->latest('created_at')->first();
        };

        $countUnreadMessages = function ($id, $type) use ($authUserId) {
            return Conversation::where('sender_id', $id)->where('sender_type', $type)
                ->where('receiver_id', $authUserId)->where('receiver_type', 'customer')
                ->where('is_read', 0)->count();
        };

        $superAdmins = $superAdmins->filter(fn($superadmin) => Conversation::where('sender_id', $superadmin->id)->where('sender_type', 'super_admin')->where('receiver_id', $authUserId)->where('receiver_type', 'customer')->exists());
        $admins = $admins->filter(fn($admin) => Conversation::where('sender_id', $admin->id)->where('sender_type', 'admin')->where('receiver_id', $authUserId)->where('receiver_type', 'customer')->exists());

        foreach ($superAdmins as $superadmin) {
            $superadmin->lastMessage = $getLastMessage($superadmin->id, 'super_admin');
            $superadmin->unreadCount = $countUnreadMessages($superadmin->id, 'super_admin');
        }

        foreach ($admins as $admin) {
            $admin->lastMessage = $getLastMessage($admin->id, 'admin');
            $admin->unreadCount = $countUnreadMessages($admin->id, 'admin');
        }

        foreach ($staff as $staffMember) {
            $staffMember->lastMessage = $getLastMessage($staffMember->id, 'staff');
            $staffMember->unreadCount = $countUnreadMessages($staffMember->id, 'staff');
        }

        $totalUnreadMessages = Conversation::where('receiver_id', $authUserId)
            ->where('receiver_type', 'customer')->where('is_read', 0)->count();

        return view('customer.chat', compact('superAdmins', 'admins', 'staff', 'totalUnreadMessages'));
    }

    public function show($id, $type)
    {
        if (!in_array($type, ['super_admin', 'admin', 'staff'])) {
            abort(403, 'Invalid chat recipient.');
        }

        $user = match ($type) {
            'super_admin' => SuperAdmin::findOrFail($id),
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
        };

        Conversation::where('sender_id', $id)->where('sender_type', $type)
            ->where('receiver_id', Auth::id())->where('receiver_type', 'customer')
            ->update(['is_read' => true]);

        $conversations = Conversation::where(function ($query) use ($id, $type) {
            $query->where('sender_id', Auth::id())->where('sender_type', 'customer')
                  ->where('receiver_id', $id)->where('receiver_type', $type);
        })->orWhere(function ($query) use ($id, $type) {
            $query->where('sender_id', $id)->where('sender_type', $type)
                  ->where('receiver_id', Auth::id())->where('receiver_type', 'customer');
        })->orderBy('created_at', 'asc')->get();

        // ✅ Transform the file_path to a full URL before sending to the view
        $conversations->transform(function ($conversation) {
            if ($conversation->file_path && !filter_var($conversation->file_path, FILTER_VALIDATE_URL)) {
                $conversation->file_path = url($conversation->file_path);
            }
            return $conversation;
        });

        return view('customer.chatting', compact('user', 'conversations', 'type'))
            ->with('receiverType', $type);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:super_admin,admin,staff,customer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx|max:30000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        if (!$request->message && !$request->file('file')) {
            return redirect()->back()->with('error', 'Message or file is required.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->hashName(); // Use hashName for unique, safe filenames
            $subfolder = 'uploads/chat_files';

            // ✅ Determine target directory based on environment
            $targetDir = App::environment('local')
                ? public_path($subfolder)
                : base_path('../public_html/' . $subfolder);

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $file->move($targetDir, $fileName);

            // ✅ Store the relative path in the database
            $filePath = $subfolder . '/' . $fileName;
        }

        Conversation::create([
            'sender_id' => $user->id,
            'sender_type' => 'customer',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'file_path' => $filePath,
            'is_read' => false,
        ]);

        return redirect()->back();
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|integer',
            'sender_type' => 'required|string',
        ]);

        Conversation::where('sender_id', $request->sender_id)
            ->where('sender_type', $request->sender_type)
            ->where('receiver_id', Auth::id())
            ->where('receiver_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}