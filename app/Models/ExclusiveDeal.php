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

        public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function orders()
{
    return $this->hasMany(Order::class);
}

    // Typically you also have:
    public function product()
{
    return $this->belongsTo(Product::class);
}
}
