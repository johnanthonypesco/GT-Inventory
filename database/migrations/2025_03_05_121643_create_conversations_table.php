<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration {
//     public function up()
//     {
//         Schema::create('conversations', function (Blueprint $table) {
//             $table->id();
//             $table->morphs('sender'); // This creates `sender_id` and `sender_type`
//             $table->foreignId('receiver_id');
//             $table->text('message')->nullable();
//             $table->string('receiver_type')->after('receiver_id')->nullable(); // 'admin', 'staff', 'super_admin'
//             $table->boolean('is_read')->default(false); // Add this line
//             $table->string('file_path')->nullable()->after('message'); // Store file path
//             $table->timestamps();
//         });
        
//     }

//     public function down()
//     {
//         Schema::dropIfExists('conversations');
//     }
// };


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->morphs('sender'); // This creates `sender_id` and `sender_type`
            $table->foreignId('receiver_id'); // Receiver ID
            $table->string('receiver_type')->nullable(); // Receiver type: 'admin', 'staff', 'super_admin'
            $table->text('message')->nullable(); // Message content
            $table->boolean('is_read')->default(false); // Read status
            $table->string('file_path')->nullable(); // Store file path
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
