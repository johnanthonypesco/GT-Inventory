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

        Schema::create('immutable_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('user_id');
            $table->string('province');
            $table->string('company');
            $table->string('employee');
            $table->date('date_ordered');
            $table->string('status');
            $table->string('generic_name');
            $table->string('brand_name');
            $table->string('form');
            $table->string('strength');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immutable_histories');
    }
};
