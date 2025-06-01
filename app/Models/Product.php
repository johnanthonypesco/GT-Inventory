<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'generic_name',
        'brand_name',
        'form',
        'strength',
        'img_file_path',
    ];  

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function exclusive_deals() {
        return $this->hasMany(ExclusiveDeal::class);
    }
        public function exclusiveDeals()
    {
        return $this->hasMany(ExclusiveDeal::class);
    }
}
