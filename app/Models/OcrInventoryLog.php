<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcrInventoryLog extends Model
{
    use HasFactory;

    protected $table = 'ocr_inventory_logs';

    protected $fillable = [
        'raw_text',
        'product_name',
        'batch_number',
        'expiry_date',
        'quantity',
        'location',
        'processed_at'
    ];

    public $timestamps = false;
}
