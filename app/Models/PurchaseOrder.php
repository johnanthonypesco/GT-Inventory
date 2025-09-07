<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'company_id',
        'po_file_path',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
