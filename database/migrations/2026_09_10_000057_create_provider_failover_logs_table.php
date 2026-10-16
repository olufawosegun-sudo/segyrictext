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
        Schema::create('provider_failover_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('fallback_provider_id')->constrained('providers')->cascadeOnDelete();
            $table->string('operation', 100)->index();
            $table->string('reason', 255);
            $table->dateTime('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_failover_logs');
    }
};