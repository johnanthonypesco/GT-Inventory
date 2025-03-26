<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLocation extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'latitude', 'longitude'];

    /**
     * Get the staff that owns this location.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}

