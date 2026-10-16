<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop dedicated_accounts table
        Schema::dropIfExists('dedicated_accounts');

        // Drop DVA columns from users table
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['gateway_customer_code', 'virtual_account_number', 'virtual_account_bank', 'virtual_account_active'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        Schema::create('dedicated_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_customer_id')->nullable();
            $table->string('gateway_customer_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_slug')->nullable();
            $table->string('account_number')->nullable()->index();
            $table->string('account_name')->nullable();
            $table->string('account_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status', 30)->default('pending');
            $table->string('currency', 10)->default('NGN');
            $table->timestamp('assigned_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('gateway_customer_code', 100)->nullable();
            $table->string('virtual_account_number', 50)->nullable();
            $table->string('virtual_account_bank', 100)->nullable();
            $table->boolean('virtual_account_active')->default(false);
        });
    }
};
