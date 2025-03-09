<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
//     public function showChat()
// {
//     $users = User::select('users.id', 'users.name')
//         ->leftJoin('conversations', function ($join) {
//             $join->on('users.id', '=', 'conversations.sender_id')
//                  ->orOn('users.id', '=', 'conversations.receiver_id');
//         })
//         ->selectRaw('users.id, users.name, MAX(conversations.created_at) as last_message_time, 
//                      (SELECT message FROM conversations 
//                       WHERE (sender_id = users.id OR receiver_id = users.id) 
//                       ORDER BY created_at DESC LIMIT 1) as last_message')
//         ->groupBy('users.id', 'users.name')
//         ->orderBy('last_message_time', 'DESC')
//         ->get();

//     return view('admin.chat', compact('users'));
// }


// public function showChat()
// {
//     $authUserId = auth()->id(); // Get the logged-in user ID

//     $users = User::select('users.id', 'users.name')
//         ->leftJoin('conversations', function ($join) {
//             $join->on('users.id', '=', 'conversations.sender_id')
//                  ->orOn('users.id', '=', 'conversations.receiver_id');
//         })
//         ->selectRaw('users.id, users.name, 
//                      MAX(conversations.created_at) as last_message_time, 
//                      (SELECT sender_id FROM conversations 
//                       WHERE (sender_id = users.id OR receiver_id = users.id) 
//                       ORDER BY created_at DESC LIMIT 1) as last_sender_id,
//                      (SELECT message FROM conversations 
//                       WHERE (sender_id = users.id OR receiver_id = users.id) 
//                       ORDER BY created_at DESC LIMIT 1) as last_message')
//         ->groupBy('users.id', 'users.name')
//         ->orderBy('last_message_time', 'DESC')
//         ->get();

//     return view('admin.chat', compact('users', 'authUserId'));
// }
public function showChat()
{
    $authUserId = auth()->id(); // Get the logged-in user ID

    $users = User::select('users.id', 'users.name')
        ->leftJoin('conversations', function ($join) {
            $join->on('users.id', '=', 'conversations.sender_id')
                 ->orOn('users.id', '=', 'conversations.receiver_id');
        })
        ->selectRaw('users.id, users.name, 
                     MAX(conversations.created_at) as last_message_time, 
                     (SELECT sender_id FROM conversations 
                      WHERE (sender_id = users.id OR receiver_id = users.id) 
                      ORDER BY created_at DESC LIMIT 1) as last_sender_id,
                     (SELECT message FROM conversations 
                      WHERE (sender_id = users.id OR receiver_id = users.id) 
                      ORDER BY created_at DESC LIMIT 1) as last_message,
                     (SELECT file_path FROM conversations 
                      WHERE (sender_id = users.id OR receiver_id = users.id) 
                      ORDER BY created_at DESC LIMIT 1) as last_file')
        ->groupBy('users.id', 'users.name')
        ->orderBy('last_message_time', 'DESC')
        ->get();

    return view('admin.chat', compact('users', 'authUserId'));
}


    public function chatWithUser($id)
    {
        $user = User::findOrFail($id); // Get the user being chatted with

        $conversations = Conversation::where(function ($query) use ($id) {
                $query->where('sender_id', auth()->id())
                      ->where('receiver_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('sender_id', $id)
                      ->where('receiver_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get(); // Get conversation history

        return view('admin.chatting', compact('user', 'conversations'));
    }

    public function store(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|integer',
        'message' => 'nullable|string',
        'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
    ]);

    $user = Auth::user();
    if (!$user) {
        return redirect()->back()->with('error', 'Unauthorized');
    }

    // Determine sender type (SuperAdmin or User)
    $isSuperAdmin = \App\Models\SuperAdmin::where('id', $user->id)->exists();
    $senderType = $isSuperAdmin ? 'super_admin' : 'user';

    // Handle File Upload
    $filePath = null;
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('chat_files', 'public');
    }

    // Ensure at least one of `message` or `file` is present
    if (!$request->message && !$filePath) {
        return redirect()->back()->with('error', 'Message or file is required');
    }

    // Save the chat message
    Conversation::create([
        'sender_id' => $user->id,
        'sender_type' => $senderType,
        'receiver_id' => $request->receiver_id,
        'message' => $request->message ?? '',
        'file_path' => $filePath,
    ]);

    return redirect()->back();
}

}
