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
            $table->id(); // ✅ Standard Laravel Primary Key (`id`)
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade'); // ✅ FK to `admins.id`
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade'); // ✅ FK to `locations.id`
            $table->string('staff_username')->unique(); // ✅ Unique Staff Username
            $table->string('email')->unique(); // ✅ Unique Email
            $table->string('password'); // ✅ Password
            $table->string('job_title')->nullable(); // ✅ Job Title (Nullable)
            $table->timestamps(); // ✅ created_at & updated_at
            $table->boolean('is_staff')->default(true); // ✅ Identifies staff
            $table->rememberToken(); // ✅ Authentication token for "Remember Me"
        });
        

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('staff');
    }
};
