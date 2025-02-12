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
        // ✅ Admins Table (References Existing `super_admins`)
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id'); // Primary Key
            $table->foreignId('s_admin_id')->constrained('super_admins', 's_admin_id')->onDelete('cascade'); // Foreign Key to super_admins
            $table->string('admin_username')->unique(); // Unique Admin Username
            $table->string('admin_email')->unique(); // Unique Email
            $table->string('admin_password'); // Password
            $table->timestamps(); // created_at & updated_at
            $table->boolean('is_admin')->default(true); // Identifies admin
            $table->rememberToken(); // Authentication token for "Remember Me"
        });

        // ✅ Password Reset Tokens for Admins
        Schema::create('admin_password_reset_tokens', function (Blueprint $table) {
            $table->string('admin_email')->primary(); // Reference Admin Email
            $table->string('token'); // Reset Token
            $table->timestamp('created_at')->nullable(); // Token Creation Time
        });

        // ✅ Sessions Table for Admins
        Schema::create('admin_sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Session ID
            $table->foreignId('admin_id')->constrained('admins', 'admin_id')->onDelete('cascade'); // Correct FK Reference
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
        Schema::dropIfExists('admin_sessions');
        Schema::dropIfExists('admin_password_reset_tokens');
        Schema::dropIfExists('admins');
    }
};
