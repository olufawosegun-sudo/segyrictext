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
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('phone_number_id')->nullable()->constrained('phone_numbers')->nullOnDelete();
            $table->foreignId('verification_session_id')->nullable()->constrained('verification_sessions')->nullOnDelete();
            $table->string('usage_type', 50)->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('amount', 19, 8);
            $table->char('currency_code', 3)->default('USD');
            $table->dateTime('occurred_at')->index();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};