<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Flat credit cost per image for this tier (deducted from main credit pool)
            $table->integer('image_credit_cost')->default(0)->after('monthly_image_tokens');
            // Max images allowed per script generation
            $table->integer('max_images_per_script')->default(5)->after('image_credit_cost');
            // Max regeneration attempts per image (spec: 2)
            $table->integer('max_regeneration_attempts')->default(2)->after('max_images_per_script');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['image_credit_cost', 'max_images_per_script', 'max_regeneration_attempts']);
        });
    }
};
