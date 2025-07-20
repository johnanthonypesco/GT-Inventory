<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to the customer
            $table->tinyInteger('rating'); // Overall rating (1-5)
            $table->text('comment')->nullable(); // The customer's written feedback
            $table->boolean('allow_public_display')->default(false); // Customer's consent
            $table->boolean('is_approved')->default(false); // Admin's approval to show on promo page
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};