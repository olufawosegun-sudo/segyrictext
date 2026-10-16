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
        Schema::create('phone_number_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_number_id')->constrained('phone_numbers')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['phone_number_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_number_services');
    }
};