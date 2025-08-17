<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // xxxx_xx_xx_xxxxxx_add_soft_deletes_to_companies_table.php

public function up(): void
{
    Schema::table('companies', function (Blueprint $table) {
        $table->softDeletes(); // <-- Add this line
    });
}

public function down(): void
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropSoftDeletes(); // <-- This handles rollbacks
    });
}
};
