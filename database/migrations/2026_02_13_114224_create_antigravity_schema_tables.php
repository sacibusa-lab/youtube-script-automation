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
        // 1. Niches Table
        Schema::create('niches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('tier', ['1', '2', '3'])->default('1');
            $table->text('description')->nullable();
            $table->float('monetization_cpm')->default(0.0);
            $table->float('rotation_weight')->default(1.0);
            $table->timestamps();
        });

        // 2. Channels Table
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('strategy_type', ['High CPM', 'High Volume', 'Series', 'Shorts', 'Documentary'])->default('Documentary');
            $table->enum('hybrid_intensity', ['30', '50', '70'])->default('50');
            $table->enum('risk_mode', ['Safe', 'Moderate', 'Aggressive'])->default('Safe');
            $table->enum('primary_niche', ['Business', 'Crime', 'Horror', 'Drama', 'Sci-Fi', 'Mythology', 'Motivation', 'Documentary', 'History', 'Mystery', 'Tech'])->default('Documentary');
            $table->enum('output_frequency', ['Daily', 'Weekly', 'Bulk'])->default('Weekly');
            $table->timestamps();
        });

        // 3. Videos Table Updates (Modify existing or add columns)
        Schema::table('videos', function (Blueprint $table) {
            // Check if column exists before adding for idempotency or rollback safety
            if (!Schema::hasColumn('videos', 'channel_id')) {
                $table->uuid('channel_id')->nullable()->after('user_id'); // FK added later if needed
            }
            if (!Schema::hasColumn('videos', 'niche_id')) {
                $table->uuid('niche_id')->nullable()->after('channel_id');
            }
            if (!Schema::hasColumn('videos', 'selected_title_index')) {
                $table->integer('selected_title_index')->nullable()->after('selected_title');
            }
            if (!Schema::hasColumn('videos', 'metadata')) {
                $table->json('metadata')->nullable()->after('selected_title_index');
            }
            if (!Schema::hasColumn('videos', 'outline')) {
                $table->json('outline')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('videos', 'script_text')) {
                $table->longText('script_text')->nullable()->after('script'); 
            }
            if (!Schema::hasColumn('videos', 'scene_json')) {
                $table->json('scene_json')->nullable()->after('script_text');
            }
            if (!Schema::hasColumn('videos', 'image_prompt_json')) {
                $table->json('image_prompt_json')->nullable()->after('scene_json');
            }
            if (!Schema::hasColumn('videos', 'thumbnail_json')) {
                $table->json('thumbnail_json')->nullable()->after('image_prompt_json');
            }
            if (!Schema::hasColumn('videos', 'similarity_score')) {
                $table->float('similarity_score')->default(0);
            }
            if (!Schema::hasColumn('videos', 'hash_signature')) {
                $table->string('hash_signature', 64)->nullable()->index();
            }
            if (!Schema::hasColumn('videos', 'monetization_tier')) {
                $table->enum('monetization_tier', ['High', 'Medium', 'Low'])->default('Medium');
            }
            if (!Schema::hasColumn('videos', 'word_count')) {
                $table->integer('word_count')->default(0);
            }
        });

        // 4. Title Patterns Table
        Schema::create('title_patterns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('niche_id')->nullable();
            $table->string('pattern_text');
            $table->float('ctr_score')->default(0.0);
            $table->integer('used_count')->default(0);
            $table->timestamps();
        });

        // 5. Previous Titles Table (Uniqueness Log)
        Schema::create('previous_titles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('channel_id')->nullable();
            $table->string('title');
            $table->timestamp('used_at')->useCurrent();
            $table->string('hash_signature', 64)->index();
        });

        // 6. Embeddings Table
        Schema::create('embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('video_id')->index(); // Assuming video implementation uses UUID or ID
            $table->json('embedding_vector');
            $table->timestamp('created_at')->useCurrent();
        });
        
        // 7. Generation Queue Table
        Schema::create('generation_queue', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('video_id')->index();
            $table->enum('stage', ['title', 'metadata', 'outline', 'script', 'scene', 'image', 'thumbnail']);
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generation_queue');
        Schema::dropIfExists('embeddings');
        Schema::dropIfExists('previous_titles');
        Schema::dropIfExists('title_patterns');
        Schema::dropIfExists('channels');
        Schema::dropIfExists('niches');
        
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'channel_id', 'niche_id', 'selected_title_index', 'metadata', 'outline', 
                'script_text', 'scene_json', 'image_prompt_json', 'thumbnail_json', 
                'similarity_score', 'hash_signature', 'monetization_tier', 'word_count'
            ]);
        });
    }
};
