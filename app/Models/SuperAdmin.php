<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'super_admins'; // ✅ Ensure this matches the migration table name

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // ✅ Standardized primary key

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        's_admin_username', // ✅ Still unique to SuperAdmins
        'email',
        'password',
        'is_super_admin', // ✅ Boolean field from migration
        'archived_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed', // ✅ Laravel automatically hashes passwords
        'is_super_admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * ✅ Relationship: A SuperAdmin has many Admins.
     */
    public function admins()
    {
        return $this->hasMany(Admin::class, 'super_admin_id'); // ✅ Use `super_admin_id` as FK
    }
    // jm added
    public function getNameAttribute()
    {
        return $this->s_admin_username;
    }
    public function groupChats()
    {
        return $this->hasMany(GroupChat::class, 'super_admin_id'); // ✅ Relationship to GroupChats
    }
    public function messages() {
        return $this->morphMany(GroupChat::class, 'sender');
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
