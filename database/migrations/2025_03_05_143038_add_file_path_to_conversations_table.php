<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('message'); // Store file path
        });
    }
    
    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
    
};
