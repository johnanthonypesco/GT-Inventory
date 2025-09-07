<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Staff;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purchase_order_id',
        'exclusive_deal_id',
        'date_ordered',
        'status',
        'quantity',
        'qr_code',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function exclusive_deal():BelongsTo {
        return $this->belongsTo(ExclusiveDeal::class, 'exclusive_deal_id', 'id');
    }
    public function exclusiveDeal()
{
    return $this->belongsTo(ExclusiveDeal::class);
}


// app/Models/Company.php
public function exclusiveDeals()
{
    return $this->hasMany(ExclusiveDeal::class);
}

protected $casts = [
    'date_ordered' => 'datetime',
];

public function staff()
{
    return $this->belongsTo(Staff::class);
}

public function purchase_order() {
    return $this->belongsTo(related: PurchaseOrder::class);
}

}


