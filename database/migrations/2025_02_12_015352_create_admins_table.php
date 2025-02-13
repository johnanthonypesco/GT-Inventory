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
            $table->id(); // ✅ Standard Laravel Primary Key (automatically `admin_id`)
            $table->foreignId('super_admin_id')->constrained('super_admins')->onDelete('cascade'); // ✅ Fixed FK (Changed `s_admin_id` → `id`)
            $table->string('username')->unique(); // ✅ Standardized field name
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(true); // ✅ Optional flag for identification
            $table->timestamps(); // ✅ created_at & updated_at
            $table->rememberToken(); // ✅ Authentication token
        });
        

        // ✅ Password Reset Tokens for Admins
       
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
