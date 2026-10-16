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
        // 1. Create number_orders table
        if (!Schema::hasTable('number_orders')) {
            Schema::create('number_orders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('idempotency_key', 100);
                $table->string('country', 10);
                $table->unsignedInteger('duration_days');
                $table->decimal('usd_price', 10, 2);
                $table->decimal('fx_rate', 12, 4);
                $table->decimal('margin_pct', 5, 2);
                $table->unsignedBigInteger('price_kobo');
                $table->string('status', 32)->default('created');
                $table->string('provider_request_id', 100)->nullable()->unique();
                $table->string('provider_status', 32)->nullable();
                $table->string('phone_number', 50)->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('failure_reason', 255)->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'idempotency_key']);
                $table->index(['user_id', 'created_at']);
                $table->index(['status', 'updated_at']);
            });
        }

        // 2. Add balance_kobo to wallets if missing
        if (Schema::hasTable('wallets') && !Schema::hasColumn('wallets', 'balance_kobo')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('balance_kobo')->default(0)->after('balance');
            });
        }

        // 3. Add kobo fields and meta to wallet_transactions if missing
        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('wallet_transactions', 'amount_kobo')) {
                    $table->bigInteger('amount_kobo')->nullable()->after('amount');
                }
                if (!Schema::hasColumn('wallet_transactions', 'balance_after_kobo')) {
                    $table->unsignedBigInteger('balance_after_kobo')->nullable()->after('balance_after');
                }
                if (!Schema::hasColumn('wallet_transactions', 'meta')) {
                    $table->json('meta')->nullable()->after('description');
                }
            });
        }

        // 4. Add order_id, body, dedupe_hash to sms_messages if missing
        if (Schema::hasTable('sms_messages')) {
            Schema::table('sms_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('sms_messages', 'order_id')) {
                    $table->uuid('order_id')->nullable()->index()->after('id');
                }
                if (!Schema::hasColumn('sms_messages', 'body')) {
                    $table->text('body')->nullable()->after('message');
                }
                if (!Schema::hasColumn('sms_messages', 'dedupe_hash')) {
                    $table->string('dedupe_hash', 64)->nullable()->unique()->after('provider_message_id');
                }
            });
        }

        // 5. Add source and event_key to webhook_events if missing
        if (Schema::hasTable('webhook_events')) {
            Schema::table('webhook_events', function (Blueprint $table) {
                if (!Schema::hasColumn('webhook_events', 'source')) {
                    $table->string('source', 50)->nullable()->after('id');
                }
                if (!Schema::hasColumn('webhook_events', 'event_key')) {
                    $table->string('event_key', 100)->nullable()->unique()->after('source');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_orders');

        if (Schema::hasTable('wallets') && Schema::hasColumn('wallets', 'balance_kobo')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropColumn('balance_kobo');
            });
        }

        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $cols = array_filter(['amount_kobo', 'balance_after_kobo', 'meta'], fn($c) => Schema::hasColumn('wallet_transactions', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('sms_messages')) {
            Schema::table('sms_messages', function (Blueprint $table) {
                $cols = array_filter(['order_id', 'body', 'dedupe_hash'], fn($c) => Schema::hasColumn('sms_messages', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('webhook_events')) {
            Schema::table('webhook_events', function (Blueprint $table) {
                $cols = array_filter(['source', 'event_key'], fn($c) => Schema::hasColumn('webhook_events', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
