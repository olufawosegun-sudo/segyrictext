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
        Schema::create('number_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('phone_number_id')->constrained('phone_numbers')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('status', 32)->default('active')->index();
            $table->dateTime('start_at');
            $table->dateTime('expires_at')->index();
            $table->decimal('price', 19, 8);
            $table->char('currency_code', 3)->default('USD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_rentals');
    }
};