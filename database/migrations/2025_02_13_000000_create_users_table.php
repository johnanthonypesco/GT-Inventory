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
        Schema::create('users', function (Blueprint $table) {
            $table->id('customer_id'); // Primary Key
            $table->foreignId('location_id')->constrained('locations', 'location_id')->cascadeOnDelete(); 
            $table->string('customer_name'); // Customer Name
            $table->string('customer_email')->unique(); // Unique Email
            $table->string('customer_password'); // Password
            $table->string('customer_cnum')->nullable(); // Contact Number
            $table->timestamps(); // created_at & updated_at
            $table->boolean('is_customer')->default(true); // Identifies Customer
            $table->rememberToken(); // Authentication token
        });
        // ✅ Password Reset Tokens for Customers
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('customer_email')->primary(); // Primary Key (Reference to Users Email)
            $table->string('token'); // Reset Token
            $table->timestamp('created_at')->nullable(); // Token Creation Time
        });
        
        // ✅ Sessions Table for Customers
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Session ID
            $table->foreignId('customer_id')->nullable()->constrained('users', 'customer_id')->onDelete('cascade'); // Corrected FK
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
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
