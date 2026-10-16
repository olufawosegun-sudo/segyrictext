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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_identifier_hash', 255)->index();
            $table->string('device_name', 150)->nullable();
            $table->string('platform', 50)->index();
            $table->string('os_version', 50)->nullable();
            $table->string('app_version', 50)->nullable();
            $table->text('push_token')->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};