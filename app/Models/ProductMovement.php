<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'inventory_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'description',
    ];

    /**
     * Get the product associated with the movement.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the specific inventory batch associated with the movement.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the user who caused the movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // NEW: Get branch from inventory
    public function branch()
    {
        return $this->inventory()->with('branch'); // assuming you have branch relationship in Inventory
    }

    // Helper to get branch name easily
    public function getBranchNameAttribute()
    {
        return $this->inventory?->branch_id == 1 ? 'RHU 1' : ($this->inventory?->branch_id == 2 ? 'RHU 2' : 'Unknown');
    }
}