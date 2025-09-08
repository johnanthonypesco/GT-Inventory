<?php

use App\Models\ExclusiveDeal;
use App\Models\GroupedOrder;
use App\Models\PurchaseOrder;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseOrder::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ExclusiveDeal::class)->constrained()->cascadeOnDelete();
            $table->integer("quantity")->default(0);
            $table->string("status")->default("pending");
            $table->date("date_ordered");
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
