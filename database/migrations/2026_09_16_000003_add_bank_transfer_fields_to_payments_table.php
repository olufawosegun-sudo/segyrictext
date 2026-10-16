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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('channel', 64)->nullable()->after('provider')->index();
            $table->string('bank_name', 128)->nullable()->after('channel');
            $table->string('account_number', 64)->nullable()->after('bank_name')->index();
            $table->string('account_name', 191)->nullable()->after('account_number');
            $table->dateTime('expires_at')->nullable()->after('paid_at')->index();
            $table->json('metadata')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'channel',
                'bank_name',
                'account_number',
                'account_name',
                'expires_at',
                'metadata',
            ]);
        });
    }
};
