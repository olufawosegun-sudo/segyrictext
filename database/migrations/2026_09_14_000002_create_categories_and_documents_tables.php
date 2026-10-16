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
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->string('title');
                $table->longText('content')->nullable();
                $table->string('format', 32)->default('markdown');
                $table->string('status', 32)->default('draft')->index();
                $table->string('origin_platform', 32)->default('web');
                $table->json('tags')->nullable();
                $table->dateTime('scheduled_at')->nullable()->index();
                $table->json('analysis_metrics')->nullable();
                $table->unsignedInteger('word_count')->default(0);
                $table->unsignedInteger('char_count')->default(0);
                $table->dateTime('processed_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('categories');
    }
};
