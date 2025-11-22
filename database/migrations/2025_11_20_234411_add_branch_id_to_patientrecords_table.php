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
    Schema::table('patientrecords', function (Blueprint $table) {
        // Default to 1 (RHU 1) for existing records
        $table->foreignId('branch_id')->default(1)->constrained('branches')->after('id'); 
    });
}

public function down()
{
    Schema::table('patientrecords', function (Blueprint $table) {
        $table->dropForeign(['branch_id']);
        $table->dropColumn('branch_id');
    });
}
};