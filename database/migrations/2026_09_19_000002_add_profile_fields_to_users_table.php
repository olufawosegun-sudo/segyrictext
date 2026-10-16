<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Guard check: detect any duplicate phone numbers among existing users
        if (Schema::hasColumn('users', 'phone')) {
            $duplicates = DB::table('users')
                ->select('phone', DB::raw('COUNT(*) as total'))
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->groupBy('phone')
                ->having('total', '>', 1)
                ->get();

            if ($duplicates->isNotEmpty()) {
                $dupeList = $duplicates->pluck('phone')->implode(', ');
                throw new RuntimeException("Cannot enforce unique constraint on 'phone': duplicate phone numbers found in users table ({$dupeList}).");
            }
        }

        // 2. Add first_name, middle_name, last_name, and phone as nullable columns
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('middle_name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 32)->nullable()->after('email');
            }
        });

        // 3. Ensure phone has a unique constraint if one doesn't already exist
        if (DB::getDriverName() === 'mysql') {
            $existingIndexes = collect(DB::select("SHOW INDEXES FROM users"))
                ->pluck('Key_name')
                ->unique();

            if (!$existingIndexes->contains('users_phone_unique')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('phone', 'users_phone_unique');
                });
            }
        } else {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('phone', 'users_phone_unique');
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('users', 'first_name')) {
                $columnsToDrop[] = 'first_name';
            }
            if (Schema::hasColumn('users', 'middle_name')) {
                $columnsToDrop[] = 'middle_name';
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $columnsToDrop[] = 'last_name';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
