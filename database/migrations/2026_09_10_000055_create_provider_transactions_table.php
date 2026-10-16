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
        Schema::create('provider_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('provider_account_id')->constrained('provider_accounts')->cascadeOnDelete();
            $table->string('type', 50)->index();
            $table->string('reference', 150)->unique();
            $table->string('provider_reference', 150)->index();
            $table->decimal('amount', 19, 8);
            $table->char('currency_code', 3)->default('USD');
            $table->string('status', 32)->default('completed')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_transactions');
    }
};