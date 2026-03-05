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
        // Layer 2: Content Structures (e.g., 3-Act, Countdown)
        Schema::create('content_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // "3-Act Structure", "Countdown"
            $table->text('description')->nullable();
            $table->text('prompt_template')->nullable(); // Specific AI instructions for this structure
            $table->timestamps();
        });

        // Layer 3: Emotional Tones (e.g., Fear, Curiosity)
        Schema::create('emotional_tones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // "Fear", "Curiosity"
            $table->string('keywords')->nullable(); // "dark, ominous, terrifying"
            $table->timestamps();
        });

        // Update Videos Table
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'content_structure_id')) {
                $table->uuid('content_structure_id')->nullable()->after('niche_id');
            }
            if (!Schema::hasColumn('videos', 'emotional_tone_id')) {
                $table->uuid('emotional_tone_id')->nullable()->after('content_structure_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['content_structure_id', 'emotional_tone_id']);
        });
        Schema::dropIfExists('emotional_tones');
        Schema::dropIfExists('content_structures');
    }
};
