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
        Schema::create('number_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_number_id')->constrained('phone_numbers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->index();
            $table->string('status', 32)->default('reserved')->index();
            $table->dateTime('reserved_at');
            $table->dateTime('expires_at')->index();
            $table->dateTime('released_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_reservations');
    }
};