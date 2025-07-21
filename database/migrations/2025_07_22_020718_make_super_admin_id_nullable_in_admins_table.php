<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method will be executed when you run the 'php artisan migrate' command.
     */
    public function up(): void
    {
        // We use Schema::table() to modify an existing table.
        Schema::table('admins', function (Blueprint $table) {
            // The change() method allows us to modify an existing column's attributes.
            // We are making the 'super_admin_id' column nullable.
            // The unsignedBigInteger type is used to match the foreignId convention.
            $table->unsignedBigInteger('super_admin_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     * This method will be executed if you ever need to roll back this migration.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // This will revert the column back to being not nullable.
            $table->unsignedBigInteger('super_admin_id')->nullable(false)->change();
        });
    }
};
