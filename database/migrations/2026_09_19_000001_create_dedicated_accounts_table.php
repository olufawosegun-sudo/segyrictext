<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dedicated_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('gateway_customer_id', 100)->nullable()->index();
            $table->string('gateway_customer_code', 100)->nullable()->index();
            $table->string('bank_name', 128);
            $table->string('bank_slug', 64)->nullable();
            $table->string('account_number', 64)->index();
            $table->string('account_name', 191);
            $table->string('account_id', 100)->nullable()->comment('Gateway DVA ID');
            $table->boolean('is_active')->default(true)->index();
            $table->char('currency', 3)->default('NGN');
            $table->dateTime('assigned_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'bank_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dedicated_accounts');
    }
};
