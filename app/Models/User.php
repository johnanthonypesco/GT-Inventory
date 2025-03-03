<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

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
        'location_id', // ✅ Foreign key to `locations` table
        'email_verified_at',
        'company_id'

    ];

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
        'password' => 'hashed', // ✅ Automatically hash passwords
    ];

    /**
     * ✅ Relationship: User belongs to a Location.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function exclusive_deals() {
        return $this->hasMany(Exclusive_Deal::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
