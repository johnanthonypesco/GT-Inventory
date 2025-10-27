<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryLogsTable extends Migration
{
    public function up()
    {
        Schema::create('history_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 60); // e.g., ADD, UPDATE, ARCHIVE, LOGIN, etc.
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->json('metadata')->nullable(); // optional structured data
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_logs');
    }
}
