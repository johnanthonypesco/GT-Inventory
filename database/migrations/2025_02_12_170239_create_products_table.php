
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Magseset ako ng max length ng mga string fields sigrae, ayaw kasi mag migrate fresh kapag walang max length
            $table->string('generic_name', 100)->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->string('form', 50)->nullable();
            $table->string('strength' , 50)->nullable();
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
