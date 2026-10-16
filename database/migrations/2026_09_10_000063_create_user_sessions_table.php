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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_token_hash', 255)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('last_activity_at')->index();
            $table->dateTime('expires_at')->index();
            $table->dateTime('revoked_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};