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
        Schema::create('financial_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('expected_amount', 19, 8);
            $table->decimal('actual_amount', 19, 8);
            $table->decimal('difference', 19, 8);
            $table->string('status', 32)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->dateTime('reconciled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reconciliations');
    }
};