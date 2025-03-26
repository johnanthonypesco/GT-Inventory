<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use GPBMetadata\Google\Type\Datetime;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users'; // ✅ Ensure table name matches migration

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // ✅ Standard Laravel Primary Key (Change from `customer_id` to `id`)

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
        'name',  // ✅ Standardized field name (was `customer_name`)
        'email',  // ✅ Standardized field name (was `customer_email`)
        'password',  // ✅ Standardized field name (was `customer_password`)
        'contact_number',  // ✅ Standardized field name (was `customer_cnum`)
        'email_verified_at',
        'company_id',
        'archived_at',];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',  // ✅ Standardized to `password`
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'archived_at' => 'datetime', // ✅ Automatically hash passwords
    ];

    /**
     * ✅ Relationship: User belongs to a Location.
     */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function orders():HasMany {
        return $this->hasMany(Order::class);
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

