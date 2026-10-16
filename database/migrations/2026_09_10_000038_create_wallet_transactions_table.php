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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 32)->index();
            $table->decimal('amount', 19, 8);
            $table->decimal('balance_before', 19, 8);
            $table->decimal('balance_after', 19, 8);
            $table->string('reference', 100)->unique();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->dateTime('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};