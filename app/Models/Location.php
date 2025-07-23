<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

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
    public function users():HasMany
    {
        return $this->hasMany(User::class, 'location_id');
    }

    /**
     * Relationship: A Location has many Staff.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'location_id');
    }
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
