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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = global character
            $table->string('name');
            $table->string('slug')->unique(); // stable reference key (e.g., "alicia-the-economist")
            $table->text('description'); // Short bio / personality overview
            $table->json('visual_traits'); // Detailed visual DNA for AI prompts
            $table->string('niche')->nullable(); // Associated niche (e.g., "US Wealth Logic")
            $table->string('reference_image_url')->nullable(); // Master reference image
            $table->string('voice_profile_id')->nullable(); // For future ElevenLabs TTS integration
            $table->boolean('is_global')->default(false); // Global starter characters visible to all users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
