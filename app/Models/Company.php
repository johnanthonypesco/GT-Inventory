<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'status'];

    // Relationship: One Company has many Users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relationship: One Company has many Orders
    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }
}
