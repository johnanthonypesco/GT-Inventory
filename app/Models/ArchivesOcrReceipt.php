<?php
// app/Models/ArchivesOcrReceipt.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivesOcrReceipt extends Model
{
    use HasFactory;

    // Itukoy ang pangalan ng table na gagamitin
    protected $table = 'archives_ocr_receipt';

    // Itukoy ang mga fields na pwedeng mass-assign
    protected $fillable = ['original_filename', 'image_path'];
}