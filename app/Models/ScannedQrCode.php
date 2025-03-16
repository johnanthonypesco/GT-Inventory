<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_name',
        'batch_number',
        'expiry_date',
        'location',
        'quantity',
        'scanned_at',
        'signature',

    ];

    public $timestamps = false;
}
