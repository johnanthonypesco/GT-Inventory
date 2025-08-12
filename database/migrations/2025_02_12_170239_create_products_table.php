
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
        // nage set ako ng max length sa mga string fields, ayaw kasi mag migrate fresh sigrare by: john anthony 
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('generic_name', 100)->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->string('form', 100)->nullable();
            $table->string('strength', 100)->nullable();
            $table->string('img_file_path')->nullable()->default('image/default-product-pic.png');
            $table->string('is_archived')->nullable()->default('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
