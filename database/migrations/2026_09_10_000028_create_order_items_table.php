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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('phone_number_id')->nullable()->constrained('phone_numbers')->nullOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('number_type_id')->constrained('number_types')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 19, 8);
            $table->decimal('total_price', 19, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};