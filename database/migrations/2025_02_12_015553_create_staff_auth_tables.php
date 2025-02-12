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
        // ✅ Staff Table (References Existing `admins` & `locations`)
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id'); // Primary Key
            $table->foreignId('admin_id')->constrained('admins', 'admin_id')->onDelete('cascade'); // FK to `admins`
            $table->foreignId('location_id')->constrained('locations', 'location_id')->onDelete('cascade'); // FK to `locations`
            $table->string('staff_username')->unique(); // Unique Staff Username
            $table->string('staff_email')->unique(); // Unique Email
            $table->string('staff_password'); // Password
            $table->string('job_title')->nullable(); // Job Title
            $table->timestamps(); // created_at & updated_at
            $table->boolean('is_staff')->default(true); // Identifies staff
            $table->rememberToken(); // Authentication token for "Remember Me"
        });

        // ✅ Password Reset Tokens for Staff
        Schema::create('staff_password_reset_tokens', function (Blueprint $table) {
            $table->string('staff_email')->primary(); // Reference Staff Email
            $table->string('token'); // Reset Token
            $table->timestamp('created_at')->nullable(); // Token Creation Time
        });

        // ✅ Sessions Table for Staff (Created After `staff` Exists)
        Schema::create('staff_sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Session ID
            $table->foreignId('staff_id')->constrained('staff', 'staff_id')->onDelete('cascade'); // Correct FK Reference
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
        Schema::dropIfExists('staff_sessions');
        Schema::dropIfExists('staff_password_reset_tokens');
        Schema::dropIfExists('staff');
    }
};
