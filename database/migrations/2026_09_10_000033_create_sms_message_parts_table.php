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
        Schema::create('sms_message_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_message_id')->constrained('sms_messages')->cascadeOnDelete();
            $table->unsignedSmallInteger('part_number');
            $table->text('message');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_message_parts');
    }
};