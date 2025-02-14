<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'username', // ✅ Change from 'admin_username' to 'username'
        'email',
        'password',
        'super_admin_id',
        'is_admin', // ✅ Ensure this is included
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password', // ✅ Laravel expects this field name
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed', // ✅ Laravel automatically handles hashing
    ];

    /**
     * Relationship: Admin belongs to a Super Admin.
     */
    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'super_admin_id'); // ✅ Correct FK
    }

    /**
     * Relationship: Admin has many Staff.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'admin_id'); // ✅ No change needed
    }
}
