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
        Schema::create('api_key_scopes', function (Blueprint $table) {
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->foreignId('api_scope_id')->constrained('api_scopes')->cascadeOnDelete();
            $table->dateTime('created_at')->useCurrent();
            $table->unique(['api_key_id', 'api_scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_key_scopes');
    }
};