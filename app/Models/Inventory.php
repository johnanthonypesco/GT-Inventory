<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'batch_number',
        'expiry_date',            
        'quantity',            
    ];

    public function location(): BelongsTo {
        return $this->belongsTo(Location::class);
    }
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
    public function acknowledgement_receipts(): HasMany {
        return $this->hasMany(Acknowledgement_Receipt::class, 'receipt_id');
    }
}
