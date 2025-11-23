<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('branch_id')->constrained();
        $table->foreignId('user_id')->constrained(); // The Pharmacist who created it
        // Status Workflow: pending_admin -> pending_finance -> approved
        $table->enum('status', ['pending_admin', 'pending_finance', 'approved', 'rejected'])->default('pending_admin');
        $table->timestamp('admin_approved_at')->nullable();
        $table->timestamp('finance_approved_at')->nullable();
        $table->text('remarks')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
