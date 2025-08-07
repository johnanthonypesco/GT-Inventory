<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmutableHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'province',
        'company',
        'employee',
        'date_ordered',
        'status',
        'generic_name',
        'brand_name',
        'form',
        'strength',
        'quantity',
        'price',
        'subtotal',
        'order_id',
    ];

    protected $casts = [
        'date_ordered' => 'datetime',
    ];
    

     public function scannedQrCode()
    {
        // This assumes your 'immutable_histories' table has an 'order_id' column
        // that matches the 'order_id' in the 'scanned_qr_codes' table.
        return $this->hasOne(ScannedQrCode::class, 'order_id', 'order_id');
    }
}
