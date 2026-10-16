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
        Schema::create('rental_sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('number_rentals')->cascadeOnDelete();
            $table->foreignId('sms_message_id')->constrained('sms_messages')->cascadeOnDelete();
            $table->dateTime('created_at')->useCurrent();
            $table->unique(['rental_id', 'sms_message_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_sms_messages');
    }
};