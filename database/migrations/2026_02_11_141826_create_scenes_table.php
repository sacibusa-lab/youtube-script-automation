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
        Schema::create('scenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->integer('scene_number');
            $table->text('narration_text');
            $table->text('visual_prompt'); // 16:9, photorealistic, cinematic
            $table->json('character_references')->nullable(); // character IDs or names
            $table->string('image_url')->nullable();
            $table->string('image_provider')->nullable(); // gemini/openai/anthropic/etc
            $table->integer('duration_seconds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenes');
    }
};
