<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Historylogs extends Model
{
    //
    use HasFactory;

    protected $fillable = ['event', 'description', 'user_email', 'created_at'];


}
