<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedQrCode extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Set to false because we are using a custom 'scanned_at' column.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_name',
        'location',
        'quantity',
        'signature',
        'scanned_at',
        'affected_batches', // ✅ Correct: batch_number and expiry_date are removed
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'affected_batches' => 'array', // This is perfect
        'scanned_at' => 'datetime',   // This is perfect
    ];

    /**
     * Get the order that owns the scanned QR code.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}