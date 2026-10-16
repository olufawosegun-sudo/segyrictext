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
        Schema::create('rental_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('number_rentals')->cascadeOnDelete();
            $table->dateTime('old_expires_at');
            $table->dateTime('new_expires_at');
            $table->decimal('amount', 19, 8);
            $table->char('currency_code', 3)->default('USD');
            $table->string('status', 32)->default('completed')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_renewals');
    }
};