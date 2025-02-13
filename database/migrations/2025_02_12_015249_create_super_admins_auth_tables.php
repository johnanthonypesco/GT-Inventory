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
            $table->id(); // Primary Key
            $table->string('s_admin_username')->unique(); // Super Admin Username
            $table->string('email')->unique(); // Unique Email
            $table->string('password'); // Password
            $table->timestamps(); // created_at & updated_at
            $table->boolean('is_super_admin')->default(true); // Identifies super admin
            $table->rememberToken(); // Authentication token for "Remember Me"
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
