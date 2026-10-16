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
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date')->index();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('total_verifications')->default(0);
            $table->unsignedInteger('successful_verifications')->default(0);
            $table->unsignedInteger('failed_verifications')->default(0);
            $table->decimal('total_revenue', 19, 8)->default(0);
            $table->decimal('total_cost', 19, 8)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};