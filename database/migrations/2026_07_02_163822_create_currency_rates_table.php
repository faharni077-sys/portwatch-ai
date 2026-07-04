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
    Schema::create('currency_rates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->cascadeOnDelete();
        $table->string('base_currency');
        $table->string('target_currency');
        $table->decimal('exchange_rate',15,6);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
