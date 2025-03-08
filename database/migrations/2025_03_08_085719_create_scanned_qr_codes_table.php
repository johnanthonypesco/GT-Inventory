<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('scanned_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('product_name');
            $table->string('batch_number');
            $table->date('expiry_date');
            $table->string('location');
            $table->integer('quantity');
            $table->timestamp('scanned_at')->useCurrent();
            
            // Add foreign key constraint
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('scanned_qr_codes');
    }
};
