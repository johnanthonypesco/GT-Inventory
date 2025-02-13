<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acknowledgement_Receipt extends Model
{
    protected $fillable = [
        'batch_number',
        'date_released',
        'date_received',
        'img_file_path',
    ];

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
