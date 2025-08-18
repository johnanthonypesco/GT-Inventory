<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('season_peak')->nullable()->after('brand_name'); // tag-ulan, tag-init, all-year
            // $table->float('trend_score')->nullable()->after('season_peak'); // Calculated score
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['season_peak']);
        });
    }
};