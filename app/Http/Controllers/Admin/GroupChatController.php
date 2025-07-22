<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\App; // Add this
use Illuminate\Support\Facades\File; // Add this

class GroupChatController extends Controller
{
    public function index()
    {
        $conversations = GroupChat::with('sender')->get();

        foreach ($conversations as $conversation) {
            // Decrypt message if it exists
            if ($conversation->message) {
                try {
                    $conversation->message = Crypt::decryptString($conversation->message);
                } catch (\Exception $e) {
                    $conversation->message = '[Could not decrypt message]';
                }
            }

            // ✅ Convert file_path from relative path to full URL for the view
            if ($conversation->file_path) {
                // Check if it's not already a full URL
                if (!filter_var($conversation->file_path, FILTER_VALIDATE_URL)) {
                    $conversation->file_path = url($conversation->file_path);
                }
            }
        }

        return view('admin.GroupChat', compact('conversations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // ✅ Use hashName for a unique, safe filename
            $fileName = $file->hashName();
            $subfolder = 'group_chats';

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

        GroupChat::create([
            'sender_id' => auth()->id(),
            'sender_type' => get_class(auth()->user()),
            'message' => $request->message ? Crypt::encryptString($request->message) : null, // 🔒 Encrypt message
            'file_path' => $filePath,
        ]);

        return back();
    }
}