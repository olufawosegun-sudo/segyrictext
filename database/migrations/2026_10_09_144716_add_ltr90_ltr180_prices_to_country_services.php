<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add supplier_ltr90_price and supplier_ltr180_price columns
     * to support 3-month (UK) and 6-month (HK, ID) rental plans
     * from the PVADeals API.
     */
    public function up(): void
    {
        if (Schema::hasTable('country_services')) {
            Schema::table('country_services', function (Blueprint $table) {
                if (!Schema::hasColumn('country_services', 'supplier_ltr90_price')) {
                    $table->decimal('supplier_ltr90_price', 10, 4)->nullable()->after('supplier_ltr30_price');
                }
                if (!Schema::hasColumn('country_services', 'supplier_ltr180_price')) {
                    $table->decimal('supplier_ltr180_price', 10, 4)->nullable()->after('supplier_ltr90_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('country_services')) {
            Schema::table('country_services', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('country_services', 'supplier_ltr90_price')) {
                    $cols[] = 'supplier_ltr90_price';
                }
                if (Schema::hasColumn('country_services', 'supplier_ltr180_price')) {
                    $cols[] = 'supplier_ltr180_price';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
