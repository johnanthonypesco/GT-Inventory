<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'sender_type', 'receiver_id', 'message', 'file_path'];


    /**
     * Polymorphic relationship for sender.
     * Sender can be either a User or a SuperAdmin.
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Receiver relationship (always a User).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
