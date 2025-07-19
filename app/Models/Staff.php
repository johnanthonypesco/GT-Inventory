<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'staff_username',
        'email',
        'password',
        'admin_id',
        'location_id',
        'job_title',
        'is_staff',
        'archived_at',
        'contact_number',
        'two_factor_code',        // <-- Idinagdag
        'two_factor_expires_at',  // <-- Idinagdag
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',        // <-- Idinagdag
        'two_factor_expires_at',  // <-- Idinagdag
    ];

    /**
     * ✅ ANG PINAKA-MAHALAGANG PAGBABAGO
     */
    protected $casts = [
        'password' => 'hashed',
        'archived_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'two_factor_code' => 'string', // 👈 Tinitiyak na string ang code
    ];

    /**
     * ✅ Binago para mag-generate ng string
     */
    public function generateTwoFactorCode()
    {
        $this->two_factor_code = (string) rand(100000, 999999);
        $this->two_factor_expires_at = now('Asia/Manila')->addMinutes(10);
        $this->save();
    }

    // Relationships and other methods...
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function stafflocation()
    {
        return $this->hasOne(StaffLocation::class, 'staff_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function archive()
    {
        return $this->update(['archived_at' => Carbon::now()]);
    }

    public function restore()
    {
        return $this->update(['archived_at' => null]);
    }
}