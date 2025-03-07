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

    public function orders()
    {
        // The second argument is the foreign key in the orders table
        return $this->hasMany(Order::class, 'exclusive_deal_id');
    }

    // Typically you also have:
    public function product()
    {
        // The second argument is the foreign key in the exclusive_deals table
        return $this->belongsTo(Product::class, 'product_id');
    }
}
