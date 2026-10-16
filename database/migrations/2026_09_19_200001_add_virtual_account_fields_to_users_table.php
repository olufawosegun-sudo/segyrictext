<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gateway_customer_code', 100)->nullable()->after('remember_token');
            $table->string('virtual_account_number', 50)->nullable()->after('gateway_customer_code');
            $table->string('virtual_account_bank', 100)->nullable()->after('virtual_account_number');
            $table->boolean('virtual_account_active')->default(false)->after('virtual_account_bank');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_customer_code',
                'virtual_account_number',
                'virtual_account_bank',
                'virtual_account_active',
            ]);
        });
    }
};
