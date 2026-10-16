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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_session_id')->constrained('verification_sessions')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('status', 32)->default('received')->index();
            $table->dateTime('received_at');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};