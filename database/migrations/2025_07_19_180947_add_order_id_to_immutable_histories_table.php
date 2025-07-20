<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('immutable_histories', function (Blueprint $table) {
            // Add the order_id column, which is essential for the relationship.
            // We'll place it after the 'id' column for convention.
            $table->unsignedBigInteger('order_id')->nullable()->after('id');

            // Optional but recommended: Add a foreign key constraint for data integrity.
            // This assumes your main 'orders' table exists.
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immutable_histories', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
