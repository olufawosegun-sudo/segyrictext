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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_number_id')->nullable()->constrained('phone_numbers')->cascadeOnDelete();
            $table->foreignId('verification_session_id')->nullable()->constrained('verification_sessions')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnDelete();
            $table->string('from_number', 32)->nullable();
            $table->text('message');
            $table->string('provider_message_id', 150)->index();
            $table->dateTime('received_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};