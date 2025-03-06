<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->morphs('sender'); // This creates `sender_id` and `sender_type`
            // $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id');
            $table->text('message');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
