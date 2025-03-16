<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'is_staff',
        'archived_at'
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
        'password' => 'hashed',
        'archived_at' => 'datetime', // Laravel auto-hashes passwords
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
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // ✅ Scope to filter archived users
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // ✅ Archive the user
    public function archive()
    {
        return $this->update(['archived_at' => Carbon::now()]);
    }

    // ✅ Restore the user
    public function restore()
    {
        return $this->update(['archived_at' => null]);
    }

}
