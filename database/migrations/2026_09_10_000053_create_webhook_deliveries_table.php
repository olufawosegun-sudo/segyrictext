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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_endpoint_id')->constrained('webhook_endpoints')->cascadeOnDelete();
            $table->string('event_type', 100);
            $table->string('event_id', 150)->index();
            $table->json('payload');
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->dateTime('next_retry_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};