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
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('number_type_id')->constrained('number_types')->cascadeOnDelete();
            $table->string('phone_number', 32)->unique();
            $table->string('provider_number_id', 150)->index();
            $table->string('status', 32)->default('available')->index();
            $table->json('capabilities')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_numbers');
    }
};