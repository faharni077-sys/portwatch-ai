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
    Schema::create('sentiment_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->cascadeOnDelete();
        $table->integer('positive')->default(0);
        $table->integer('neutral')->default(0);
        $table->integer('negative')->default(0);
        $table->enum('sentiment', ['Positive', 'Neutral', 'Negative']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiment_results');
    }
};
