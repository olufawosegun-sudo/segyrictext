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
        Schema::create('provider_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('provider_account_id')->constrained('provider_accounts')->cascadeOnDelete();
            $table->string('status', 32)->index();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->dateTime('checked_at')->index();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_health_checks');
    }
};