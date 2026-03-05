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
        Schema::table('videos', function (Blueprint $table) {
            $table->json('strategies')->nullable()->after('bible_data');
            $table->json('monthly_plan')->nullable()->after('strategies');
            $table->json('platform_data')->nullable()->after('monthly_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['strategies', 'monthly_plan', 'platform_data']);
        });
    }
};
