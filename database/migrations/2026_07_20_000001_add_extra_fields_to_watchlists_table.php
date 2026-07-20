<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            // Guard: only add columns that do not already exist.
            // This makes the migration safe to re-run on Railway re-deploys.
            if (! Schema::hasColumn('watchlists', 'priority')) {
                $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->default('MEDIUM')->after('country_id');
            }
            if (! Schema::hasColumn('watchlists', 'country_name')) {
                $table->string('country_name')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('watchlists', 'country_code')) {
                $table->string('country_code', 10)->nullable()->after('country_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $columns = [];
            foreach (['priority', 'country_name', 'country_code'] as $col) {
                if (Schema::hasColumn('watchlists', $col)) {
                    $columns[] = $col;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
