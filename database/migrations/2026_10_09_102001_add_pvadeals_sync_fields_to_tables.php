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
        // 1. Add sync & best value fields to country_services
        if (Schema::hasTable('country_services')) {
            Schema::table('country_services', function (Blueprint $table) {
                if (!Schema::hasColumn('country_services', 'pva_service_id')) {
                    $table->string('pva_service_id', 100)->nullable()->index()->after('service_id');
                }
                if (!Schema::hasColumn('country_services', 'supplier_str_price')) {
                    $table->decimal('supplier_str_price', 10, 4)->nullable()->after('pva_service_id');
                }
                if (!Schema::hasColumn('country_services', 'supplier_ltr3_price')) {
                    $table->decimal('supplier_ltr3_price', 10, 4)->nullable()->after('supplier_str_price');
                }
                if (!Schema::hasColumn('country_services', 'supplier_ltr7_price')) {
                    $table->decimal('supplier_ltr7_price', 10, 4)->nullable()->after('supplier_ltr3_price');
                }
                if (!Schema::hasColumn('country_services', 'supplier_ltr14_price')) {
                    $table->decimal('supplier_ltr14_price', 10, 4)->nullable()->after('supplier_ltr7_price');
                }
                if (!Schema::hasColumn('country_services', 'supplier_ltr30_price')) {
                    $table->decimal('supplier_ltr30_price', 10, 4)->nullable()->after('supplier_ltr14_price');
                }
                if (!Schema::hasColumn('country_services', 'customer_str_price_kobo')) {
                    $table->unsignedBigInteger('customer_str_price_kobo')->nullable()->after('supplier_ltr30_price');
                }
                if (!Schema::hasColumn('country_services', 'str_stock')) {
                    $table->integer('str_stock')->default(0)->after('customer_str_price_kobo');
                }
                if (!Schema::hasColumn('country_services', 'ltr_stock')) {
                    $table->integer('ltr_stock')->default(0)->after('str_stock');
                }
                if (!Schema::hasColumn('country_services', 'is_best_value')) {
                    $table->boolean('is_best_value')->default(false)->index()->after('ltr_stock');
                }
                if (!Schema::hasColumn('country_services', 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable()->after('is_best_value');
                }
                if (!Schema::hasColumn('country_services', 'metadata')) {
                    $table->json('metadata')->nullable()->after('last_synced_at');
                }
            });
        }

        // 2. Add best_value & last_synced_at to countries
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                if (!Schema::hasColumn('countries', 'is_best_value')) {
                    $table->boolean('is_best_value')->default(false)->index()->after('is_active');
                }
                if (!Schema::hasColumn('countries', 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable()->after('is_best_value');
                }
            });
        }

        // 3. Add best_value & category to services
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'category')) {
                    $table->string('category', 100)->nullable()->after('description');
                }
                if (!Schema::hasColumn('services', 'is_best_value')) {
                    $table->boolean('is_best_value')->default(false)->index()->after('is_active');
                }
                if (!Schema::hasColumn('services', 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable()->after('is_best_value');
                }
            });
        }

        // 4. Create pva_deals_sync_logs table
        if (!Schema::hasTable('pva_deals_sync_logs')) {
            Schema::create('pva_deals_sync_logs', function (Blueprint $table) {
                $table->id();
                $table->string('status', 32)->index();
                $table->unsignedInteger('services_count')->default(0);
                $table->unsignedInteger('countries_count')->default(0);
                $table->unsignedInteger('updated_count')->default(0);
                $table->unsignedInteger('created_count')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pva_deals_sync_logs');

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn(['category', 'is_best_value', 'last_synced_at']);
            });
        }

        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropColumn(['is_best_value', 'last_synced_at']);
            });
        }

        if (Schema::hasTable('country_services')) {
            Schema::table('country_services', function (Blueprint $table) {
                $table->dropColumn([
                    'pva_service_id', 'supplier_str_price', 'supplier_ltr3_price',
                    'supplier_ltr7_price', 'supplier_ltr14_price', 'supplier_ltr30_price',
                    'customer_str_price_kobo', 'str_stock', 'ltr_stock',
                    'is_best_value', 'last_synced_at', 'metadata'
                ]);
            });
        }
    }
};
