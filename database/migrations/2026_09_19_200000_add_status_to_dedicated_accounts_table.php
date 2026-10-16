<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dedicated_accounts', function (Blueprint $table) {
            // Add lifecycle status column for async DVA flow (pending → active → deactivated)
            $table->string('status', 20)->default('pending')->after('is_active')->index();
        });

        // Make account_number nullable — the async /assign endpoint doesn't return it immediately
        Schema::table('dedicated_accounts', function (Blueprint $table) {
            $table->string('account_number', 64)->nullable()->change();
            $table->string('account_name', 191)->nullable()->change();
            $table->string('bank_name', 128)->nullable()->change();
        });

        // Existing accounts that are active should have status = 'active'
        \Illuminate\Support\Facades\DB::table('dedicated_accounts')
            ->where('is_active', true)
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('dedicated_accounts', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('dedicated_accounts', function (Blueprint $table) {
            $table->string('account_number', 64)->nullable(false)->change();
            $table->string('account_name', 191)->nullable(false)->change();
            $table->string('bank_name', 128)->nullable(false)->change();
        });
    }
};
