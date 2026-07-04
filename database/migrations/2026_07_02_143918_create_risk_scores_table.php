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
    Schema::create('risk_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->onDelete('cascade');
        $table->integer('weather_risk')->default(0);
        $table->integer('inflation_risk')->default(0);
        $table->integer('currency_risk')->default(0);
        $table->integer('news_risk')->default(0);
        $table->integer('total_risk')->default(0);
        $table->string('risk_level')->default('Low');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
