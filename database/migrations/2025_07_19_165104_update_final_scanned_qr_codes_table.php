<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This applies all the final changes.
     */
    public function up(): void
    {
        Schema::table('scanned_qr_codes', function (Blueprint $table) {

            // ✅ 2. Add the new 'affected_batches' column for the detailed audit trail
            $table->json('affected_batches')->nullable()->after('signature');

            // ✅ 3. Remove the old, now-redundant columns
            $table->dropColumn(['batch_number', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     * This safely undoes all the changes if you ever need to.
     */
    public function down(): void
    {
        Schema::table('scanned_qr_codes', function (Blueprint $table) {
            // Re-create the old columns in their original positions
            $table->string('batch_number')->after('product_name');
            $table->date('expiry_date')->after('batch_number');

            // Drop the new columns that were added
            $table->dropColumn(['signature', 'affected_batches']);
        });
    }
};