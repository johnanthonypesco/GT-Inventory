<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcrInventoryLogsTable extends Migration
{
    public function up()
    {
        Schema::create('ocr_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->text('raw_text'); // Store the full OCR text
            $table->string('product_name')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('processed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ocr_inventory_logs');
    }
}
