<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('news_cache', 'sentiment')) {

            Schema::table('news_cache', function (Blueprint $table) {
                $table->string('sentiment')->nullable()->after('published_at');
            });

        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('news_cache', 'sentiment')) {

            Schema::table('news_cache', function (Blueprint $table) {
                $table->dropColumn('sentiment');
            });

        }
    }
};