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
        Schema::create('verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_session_id')->constrained('verification_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('attempt_number');
            $table->string('status', 32)->index();
            $table->string('failure_reason', 255)->nullable();
            $table->dateTime('attempted_at')->index();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_attempts');
    }
};