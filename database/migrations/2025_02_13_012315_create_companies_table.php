<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Location::class)->constrained('locations')->cascadeOnDelete(); // ✅ FK to `locations.id`
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
            // Optionally, for soft deletes:
            // $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
