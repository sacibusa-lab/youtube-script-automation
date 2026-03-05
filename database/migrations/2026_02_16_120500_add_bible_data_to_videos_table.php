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
            // Bible Logic System
            $table->json('bible_data')->nullable()->after('character_profiles');
            
            // Niche Template Tracking
            $table->string('niche_template_used')->nullable()->after('bible_data');
            $table->json('diverse_name_pool')->nullable()->after('niche_template_used');
            
            // Era-Based Story Support (for Conspiracy, Adventure, Discovery)
            $table->string('century')->nullable()->after('diverse_name_pool');
            $table->integer('specific_year')->nullable()->after('century');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'bible_data',
                'niche_template_used',
                'diverse_name_pool',
                'century',
                'specific_year'
            ]);
        });
    }
};
