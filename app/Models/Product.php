<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
}
