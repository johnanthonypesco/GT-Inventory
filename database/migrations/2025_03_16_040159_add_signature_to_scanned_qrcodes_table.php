<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('scanned_qr_codes', function (Blueprint $table) {
            $table->string('signature')->nullable()->after('order_id'); // Store signature image path
        });
    }

    public function down()
    {
        Schema::table('scanned_qr_codes', function (Blueprint $table) {
            $table->dropColumn('signature');
        });
    }

};
