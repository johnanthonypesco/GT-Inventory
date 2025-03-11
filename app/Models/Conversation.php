<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'sender_type', 'receiver_id', 'receiver_type', 'message', 'file_path'];

    /**
     * Polymorphic relationship for sender.
     * Sender can be either a User, Admin, Staff, or SuperAdmin.
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic relationship for receiver.
     * Receiver can be an Admin, Staff, or SuperAdmin (NOT a Customer).
     */
    public function receiver()
    {
        return $this->morphTo();
    }
}
