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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('topic');
            $table->string('niche');
            $table->string('sub_niche')->nullable();
            $table->enum('tier1_country', ['USA', 'UK', 'Canada', 'Australia']);
            $table->integer('duration_minutes'); // 30, 45, or 60
            $table->integer('chapter_count')->default(10);
            $table->enum('status', [
                'pending',
                'script_generated',
                'images_generated',
                'exported',
                'published'
            ])->default('pending');
            $table->json('script')->nullable(); // chapters with scenes
            $table->json('character_profiles')->nullable(); // character names, descriptions, visual references
            $table->json('title_variations')->nullable(); // array of 5 titles
            $table->string('selected_title')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->text('mega_hook')->nullable(); // first 30-second script
            $table->string('final_video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('facebook_video_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
