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
        Schema::create('country_regions', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['country_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_regions');
    }
};