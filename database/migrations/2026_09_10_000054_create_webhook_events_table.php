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
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnDelete();
            $table->string('event_id', 150)->nullable()->unique();
            $table->string('event_type', 100)->index();
            $table->json('payload');
            $table->string('status', 32)->default('pending')->index();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};