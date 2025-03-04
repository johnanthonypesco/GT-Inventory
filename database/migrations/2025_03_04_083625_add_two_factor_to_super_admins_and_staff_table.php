<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Add 2FA columns to the Super Admins table
        Schema::table('super_admins', function (Blueprint $table) {
            $table->string('two_factor_code')->nullable()->after('password'); // Stores the 6-digit code
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code'); // Expiry timestamp
        });

        // ✅ Add 2FA columns to the Staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->string('two_factor_code')->nullable()->after('password'); // Stores the 6-digit code
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code'); // Expiry timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ Remove 2FA columns from the Super Admins table
        Schema::table('super_admins', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at']);
        });

        // ✅ Remove 2FA columns from the Staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at']);
        });
    }
};
