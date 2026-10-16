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
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('method', 10);
            $table->string('endpoint', 255)->index();
            $table->unsignedSmallInteger('status_code')->index();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->dateTime('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_requests');
    }
};