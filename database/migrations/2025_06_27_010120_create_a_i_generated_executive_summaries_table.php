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
        Schema::create('ai_generated_executive_summaries', function (Blueprint $table) {
            $table->id();
            $table->json('summary_data'); // Dito natin ilalagay ang JSON response ng AI
            $table->timestamps(); // Magdadagdag ng created_at at updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generated_executive_summaries');
    }
};