<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'staff_username', // Standardized field name
        'email',
        'password',
        'admin_id',
        'location_id',
        'job_title',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password', // Laravel standard for password column
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed', // Laravel auto-hashes passwords
    ];

    /**
     * Define the relationship with the Admin model.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Define the relationship with the Location model.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
