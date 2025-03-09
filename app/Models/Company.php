<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'address', 
        'status',
        'location_id',
    ];

    public function location():BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // Relationship: One Company has many Users
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function exclusive_deals():HasMany
    {
        return $this->hasMany(ExclusiveDeal::class);
    }



    // Relationship: One Company has many Orders
    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }
}
