<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Locations Table
        Schema::create('locations', function (Blueprint $table) {
            $table->id(); // ✅ Standard Laravel Primary Key (auto-incrementing `id`)
            $table->string('province');
            $table->string('city');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
