<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // ✅ Import User model

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'message', 'is_read'];

    // ✅ Set default values for attributes
    protected $attributes = [
        'is_read' => false, // Default to unread messages
    ];

    // ✅ Relationship with sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ✅ Relationship with receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
