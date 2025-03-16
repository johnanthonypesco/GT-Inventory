<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivedAtToAccounts extends Migration
{
    public function up()
    {
        foreach (['users', 'super_admins', 'admins', 'staff'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->timestamp('archived_at')->nullable()->after('updated_at'); // Add archived_at column
            });
        }
    }

    public function down()
    {
        foreach (['users', 'super_admins', 'admins', 'staff'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('archived_at'); // Remove archived_at column if rollback is needed
            });
        }
    }
}

