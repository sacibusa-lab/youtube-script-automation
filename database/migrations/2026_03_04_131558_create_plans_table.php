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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('monthly_credits', 15, 2)->default(0);
            $table->integer('max_tokens_per_request')->default(0);
            $table->integer('concurrent_jobs')->default(1);
            $table->integer('batch_generation_limit')->default(1);
            $table->boolean('bulk_upload')->default(false);
            $table->boolean('series_memory')->default(false);
            $table->decimal('rollover_percent', 5, 2)->default(0);
            $table->boolean('api_access')->default(false);
            $table->integer('team_members')->default(0);
            $table->boolean('priority_queue')->default(false);
            $table->boolean('direct_support')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
