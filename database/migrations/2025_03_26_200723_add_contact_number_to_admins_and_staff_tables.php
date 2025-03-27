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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->unique();
        });
    
        Schema::table('staff', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->unique();
        });
    }
    
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('contact_number');
        });
    
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('contact_number');
        });
    }
};  