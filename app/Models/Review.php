<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'allow_public_display',
            'is_approved', 

    ];

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}