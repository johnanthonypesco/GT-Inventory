<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_archives_ocr_receipt_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives_ocr_receipt', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('original_filename'); // Para malaman ang original na pangalan ng file
            $table->string('image_path'); // Ang path kung saan naka-save ang file
            $table->timestamps(); // created_at at updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives_ocr_receipt');
    }
};