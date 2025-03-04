<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExclusiveDeal extends Model
{
    use HasFactory;

    protected $table = 'exclusive_deals';

    protected $fillable = [
        "company_id",
        "product_id",
        "deal_type",
        "price"
    ];

    public function company():BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function product():BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function order():HasMany {
        return $this->hasMany(Order::class);
    }
}
