<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'location_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'province',
        'city',
    ];

    /**
     * Relationship: A Location has many Users (Customers).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'location_id');
    }

    /**
     * Relationship: A Location has many Staff.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'location_id');
    }
}
//