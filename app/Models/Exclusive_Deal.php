<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exclusive_Deal extends Model
{
    protected $table = 'exclusive_deals';

    protected $fillable = [
        "user_id",
        "product_id",
        "deal_type",
        "price"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
