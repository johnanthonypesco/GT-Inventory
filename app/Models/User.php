<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
}
