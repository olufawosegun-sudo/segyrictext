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
        Schema::create('scheduled_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type', 100)->index();
            $table->string('reference_type', 150)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->dateTime('scheduled_at')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_operations');
    }
};