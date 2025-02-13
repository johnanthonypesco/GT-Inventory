<?php

use App\Models\Product;
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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->foreignIdFor(Product::class)->cascadeOnDelete();
            $table->string('batch_number');            
            $table->string('expiry_date');            
            $table->integer('quantity');            
            $table->string('status')->default("in-stock");            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
