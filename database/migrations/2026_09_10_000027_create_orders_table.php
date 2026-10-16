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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_number', 50)->unique();
            $table->string('status', 32)->default('pending')->index();
            $table->decimal('subtotal', 19, 8)->default(0);
            $table->decimal('discount_amount', 19, 8)->default(0);
            $table->decimal('tax_amount', 19, 8)->default(0);
            $table->decimal('total_amount', 19, 8)->default(0);
            $table->char('currency_code', 3)->default('USD');
            $table->string('idempotency_key', 100)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};