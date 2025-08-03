<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupChat;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class GroupChatController extends Controller
{
    /**
     * Display the chat view with initial messages.
     */
    public function index()
    {
        return view('admin.GroupChat');
    }

    /**
     * Store a new chat message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:5120', // 5MB max
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Use hashName for a unique, safe filename
            $fileName = $file->hashName();
            $subfolder = 'group_chats';

            // ✅ Determine target directory based on environment
            if (App::environment('local')) {
                $targetDir = public_path($subfolder);
            } else {
                // This assumes your Laravel project is one level inside the root,
                // and public_html is the public directory at the root.
                $targetDir = base_path('../public_html/' . $subfolder);
            }

            // Create directory if it doesn't exist
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            // Move the file to the target directory
            $file->move($targetDir, $fileName);

            // Store the relative path in the database
            $filePath = $subfolder . '/' . $fileName;
        }

        if (!$request->message && !$filePath) {
            return response()->json(['error' => 'A message or file is required.'], 422);
        }

        $chat = GroupChat::create([
            'sender_id'   => auth()->id(),
            'sender_type' => get_class(auth()->user()),
            'message'     => $request->message ? Crypt::encryptString($request->message) : null,
            'file_path'   => $filePath,
        ]);

        return response()->json($this->formatMessageForResponse($chat));
    }

    /**
     * Fetch messages for the chatbox.
     */
    public function fetchMessages(Request $request)
    {
        $lastId = $request->query('last_id', 0);

        $messages = GroupChat::with('sender')
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        $formattedMessages = $messages->map(fn($message) => $this->formatMessageForResponse($message));

        return response()->json($formattedMessages);
    }

    /**
     * Helper function to format a message for a consistent JSON response.
     */
    private function formatMessageForResponse(GroupChat $chat)
    {
        $user = auth()->user();
        $isCurrentUser = $user->id === $chat->sender_id && get_class($user) === $chat->sender_type;

        if ($isCurrentUser) {
            $senderName = "You";
        } else {
            // Eager load the sender relationship if it's not already loaded
            $chat->loadMissing('sender');

            if ($chat->sender) {
                // --- FIX IS HERE ---
                // Instead of returning a generic role, we now get the specific username from the related model.
                $senderName = match ($chat->sender_type) {
                    Staff::class      => $chat->sender->staff_username ?? 'Staff',
                    Admin::class      => $chat->sender->username ?? 'Admin',
                    SuperAdmin::class => $chat->sender->s_admin_username ?? 'Super Admin',
                    default           => 'Unknown User',
                };
            } else {
                $senderName = 'Unknown User'; // Fallback if sender is not found
            }
        }

        return [
            'id'              => $chat->id,
            'sender_name'     => $senderName,
            'message'         => $chat->message ? Crypt::decryptString($chat->message) : null,
            'file_url'        => $chat->file_path ? url($chat->file_path) : null,
            'is_current_user' => $isCurrentUser,
            'timestamp'       => Carbon::parse($chat->created_at)->format('M d, Y, h:i A'),
        ];
    }
}
