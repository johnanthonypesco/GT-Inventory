<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'batch_number',
        'product_id',
        'expiry_date',            
        'quantity',            
        'status',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function acknowledgement_receipts() {
        return $this->hasMany(Acknowledgement_Receipt::class, 'receipt_id');
    }
}
