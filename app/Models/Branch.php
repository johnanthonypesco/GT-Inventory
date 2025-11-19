<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    // Allow these columns to be mass-assigned
    protected $fillable = [
        'name',
    ];

    // ==========================
    // RELATIONSHIPS
    // ==========================

    // A branch has many users (staff)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A branch has many inventory items
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    // A branch has many orders
    public function orders()
    {
        return $this->hasMany(Order::class); 
    }
    
    // Add any other tables you have here (e.g., Reports, Patients)
}