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
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->unsignedInteger('limit_per_minute');
            $table->unsignedInteger('limit_per_hour');
            $table->unsignedInteger('limit_per_day');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
    }
};