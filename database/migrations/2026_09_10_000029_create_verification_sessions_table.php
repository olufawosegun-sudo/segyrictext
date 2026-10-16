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
        Schema::create('verification_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('phone_number_id')->constrained('phone_numbers')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->string('status', 32)->default('active')->index();
            $table->string('verification_code', 20)->nullable();
            $table->dateTime('expires_at')->index();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_sessions');
    }
};