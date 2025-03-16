<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message',
        'file_path',
        'is_read',
    ];

    /**
     * Polymorphic relationship for the sender.
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic relationship for the receiver.
     */
    public function receiver()
    {
        return $this->morphTo();
    }

    /**
     * Get all conversations involving the current model (either as sender or receiver).
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    /**
     * Scope to get unread messages for a specific user.
     */
    public function scopeUnreadMessages($query, $userId, $userType)
    {
        return $query->where('receiver_id', $userId)
            ->where('receiver_type', $userType)
            ->where('is_read', false);
    }

    /**
     * Mark messages as read for a specific conversation.
     */
    public static function markAsRead($senderId, $senderType, $receiverId, $receiverType)
    {
        self::where('sender_id', $senderId)
            ->where('sender_type', $senderType)
            ->where('receiver_id', $receiverId)
            ->where('receiver_type', $receiverType)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    protected $dates = ['created_at', 'updated_at'];
}