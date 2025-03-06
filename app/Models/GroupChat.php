<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SuperAdmin;
use App\Models\Staff;
use App\Models\Admin;
use App\Models\User;

class GroupChat extends Model {
    use HasFactory;

    protected $fillable = ['sender_id', 'sender_type', 'message', 'file_path'];
    public $timestamps = false;

    public function sender() {
        return $this->morphTo();
    }
}




