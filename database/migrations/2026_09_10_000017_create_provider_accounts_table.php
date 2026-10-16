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
        Schema::create('provider_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->string('name', 100);
            $table->json('credentials');
            $table->string('status', 32)->default('active')->index();
            $table->decimal('balance', 19, 8)->default(0);
            $table->char('currency_code', 3)->default('USD');
            $table->dateTime('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_accounts');
    }
};