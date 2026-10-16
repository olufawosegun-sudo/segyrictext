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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('quote_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('rate', 24, 12);
            $table->string('source', 100);
            $table->dateTime('effective_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};