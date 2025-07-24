<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // in database/migrations/....php
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Foreign key to the 'staff' table. Nullable because it's not always assigned.
        $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null')->after('status');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['staff_id']);
        $table->dropColumn('staff_id');
    });
}
};