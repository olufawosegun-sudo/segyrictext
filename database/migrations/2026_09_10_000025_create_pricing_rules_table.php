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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('number_type_id')->nullable()->constrained('number_types')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->decimal('purchase_price', 19, 8);
            $table->decimal('selling_price', 19, 8);
            $table->char('currency_code', 3)->default('USD')->index();
            $table->unsignedInteger('priority')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};