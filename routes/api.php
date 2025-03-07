<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

Route::get('/customer/chat/fetch-messages', function (Request $request) {
    $lastId = $request->query('last_id', 16);
    $superAdminId = 4; // ✅ Palitan ng dynamic kung may multiple SuperAdmins

    $newMessages = Conversation::where('id', '>', $lastId)
        ->where(function ($query) use ($superAdminId) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $superAdminId)
                  ->orWhere('sender_id', $superAdminId)->where('receiver_id', Auth::id());
        })
        ->orderBy('id')
        ->get();

    return response()->json($newMessages);
});
