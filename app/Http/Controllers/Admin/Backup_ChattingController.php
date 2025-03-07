<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChattingController extends Controller
{
    /**
     * Show chat with a specific user.
     */
//     public function chatWithUser($id)
//     {
//         $user = User::findOrFail($id); // Get the user being chatted with

//         $conversations = Conversation::where(function ($query) use ($id) {
//                 $query->where('sender_id', auth()->id())
//                       ->where('receiver_id', $id);
//             })
//             ->orWhere(function ($query) use ($id) {
//                 $query->where('sender_id', $id)
//                       ->where('receiver_id', auth()->id());
//             })
//             ->orderBy('created_at', 'asc')
//             ->get(); // Get conversation history

//         return view('admin.chatting', compact('user', 'conversations'));
//     }

//     public function store(Request $request)
// {
//     $request->validate([
//         'receiver_id' => 'required|integer',
//         'message' => 'nullable|string',
//         'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
//     ]);

//     $user = Auth::user();
//     if (!$user) {
//         return redirect()->back()->with('error', 'Unauthorized');
//     }

//     // Determine sender type (SuperAdmin or User)
//     $isSuperAdmin = \App\Models\SuperAdmin::where('id', $user->id)->exists();
//     $senderType = $isSuperAdmin ? 'super_admin' : 'user';

//     // Handle File Upload
//     $filePath = null;
//     if ($request->hasFile('file')) {
//         $filePath = $request->file('file')->store('chat_files', 'public');
//     }

//     // Ensure at least one of `message` or `file` is present
//     if (!$request->message && !$filePath) {
//         return redirect()->back()->with('error', 'Message or file is required');
//     }

//     // Save the chat message
//     Conversation::create([
//         'sender_id' => $user->id,
//         'sender_type' => $senderType,
//         'receiver_id' => $request->receiver_id,
//         'message' => $request->message ?? '',
//         'file_path' => $filePath,
//     ]);

//     return redirect()->back();
// }
}
// ✅ Gumamit na ang morphTo() sa sender() para sa SuperAdmin at User
// ✅ Nilagyan ng sender_type sa database para alam kung User o SuperAdmin
// ✅ Sa controller, automatic na dinedetect kung SuperAdmin o User ang sender
// ✅ Sa AppServiceProvider.php, ginamit ang morphMap() para mas madaling gamitin

//     public function storeMessage(Request $request)
// {
//     $request->validate([
//         'receiver_id' => 'required|integer',
//         'message' => 'required|string',
//     ]);

//     $user = Auth::user();

//     if (!$user) {
//         return response()->json(['error' => 'Unauthorized'], 401);
//     }

//     $isSuperAdmin = SuperAdmin::where('id', $user->id)->exists();
//     $senderType = $isSuperAdmin ? 'super_admin' : 'user';

//     $chat = Conversation::create([
//         'sender_id' => $user->id,
//         'sender_type' => $senderType,
//         'receiver_id' => $request->receiver_id,
//         'message' => $request->message,
//     ]);

//     // Return HTML for the new message (to append in chatbox)
//     return response()->json([
//         'html' => view('components.chat-message', compact('chat'))->render()
//     ]);
// } ?AJAX DISPLAY DEBUGING

    /**
     * Store a new message.
     */
// public function storeMessage(Request $request)
// {
//     $request->validate([
//         'receiver_id' => 'required|integer',
//         'message' => 'required|string',
//     ]);

//     $user = Auth::user();

//     if (!$user) {
//         return redirect()->back()->with('error', 'Unauthorized');
//     }

//     $isSuperAdmin = SuperAdmin::where('id', $user->id)->exists();
//     $senderType = $isSuperAdmin ? 'super_admin' : 'user';

//     // Save the message
//     Conversation::create([
//         'sender_id' => $user->id,
//         'sender_type' => $senderType,
//         'receiver_id' => $request->receiver_id,
//         'message' => $request->message,
//     ]);

//     // Redirect back with a success message
//     return redirect()->back()->with('success', 'Message sent!');
// }





    

