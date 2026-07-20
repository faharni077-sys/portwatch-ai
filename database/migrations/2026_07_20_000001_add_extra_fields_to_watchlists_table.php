<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            // Priority level set by user
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->default('MEDIUM')->after('country_id');
            // Denormalized country name & code — fallback when country_id has no match
            $table->string('country_name')->nullable()->after('priority');
            $table->string('country_code', 10)->nullable()->after('country_name');
        });
    }

    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropColumn(['priority', 'country_name', 'country_code']);
        });
    }
};
