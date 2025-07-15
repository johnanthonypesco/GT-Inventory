<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImmutableHistory extends Model
{

    protected $fillable = [
        'province',
        'company',
        'employee',
        'date_ordered',
        'status',
        'generic_name',
        'brand_name',
        'form',
        'quantity',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'date_ordered' => 'datetime',
    ];
}
