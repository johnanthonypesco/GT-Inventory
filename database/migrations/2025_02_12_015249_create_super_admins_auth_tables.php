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
        // Super Admins Table
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id('s_admin_id'); // Primary Key
            $table->string('s_admin_username')->unique(); // Super Admin Username
            $table->string('s_admin_email')->unique(); // Unique Email
            $table->string('s_admin_password'); // Password
            $table->timestamps(); // created_at & updated_at
            $table->boolean('is_super_admin')->default(true); // Identifies super admin
            $table->rememberToken(); // Authentication token for "Remember Me"
        });

        // Password Reset Tokens for Super Admins
        Schema::create('super_admin_password_reset_tokens', function (Blueprint $table) {
            $table->string('s_admin_email')->primary(); // Reference Super Admin Email
            $table->string('token'); // Reset Token
            $table->timestamp('created_at')->nullable();
        });

        // Sessions Table for Super Admins
        Schema::create('super_admin_sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Session ID
            $table->foreignId('s_admin_id')->nullable()->constrained('super_admins', 's_admin_id')->onDelete('cascade'); // Explicitly referencing `s_admin_id`
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload'); // Session data
            $table->integer('last_activity')->index(); // Last activity timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admin_sessions');
        Schema::dropIfExists('super_admin_password_reset_tokens');
        Schema::dropIfExists('super_admins');
    }
};
