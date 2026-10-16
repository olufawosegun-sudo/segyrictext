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
        Schema::create('phone_number_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_number_id')->constrained('phone_numbers')->cascadeOnDelete();
            $table->string('old_status', 32);
            $table->string('new_status', 32);
            $table->string('reason', 255)->nullable();
            $table->dateTime('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_number_status_history');
    }
};