<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('group_chats', function (Blueprint $table) {
        $table->id();
        $table->morphs('sender'); // ✅ Polymorphic sender (id + type)
        $table->text('message')->nullable();
        $table->string('file_path')->nullable(); // ✅ Path for uploaded file
        $table->timestamp('created_at');
        
    });
    
    
}

    public function down() {
        Schema::dropIfExists('group_chats');
    }
};
