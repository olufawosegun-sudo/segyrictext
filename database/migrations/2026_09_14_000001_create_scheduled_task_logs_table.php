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
        Schema::create('scheduled_task_logs', function (Blueprint $table) {
            $table->id();
            $table->string('task_name')->index();
            $table->string('command')->index();
            $table->string('status', 32)->default('running')->index();
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedInteger('records_affected')->default(0);
            $table->dateTime('started_at')->index();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_task_logs');
    }
};
