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
        Schema::table('generated_titles', function (Blueprint $table) {
            // Drop the old global unique index
            // Note: In Laravel/MySQL, the index name for a unique constraint unique('hash') is usually 'generated_titles_hash_unique'
            $table->dropUnique(['hash']);
            
            // Add a new unique index per video
            $table->unique(['video_id', 'hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_titles', function (Blueprint $table) {
            $table->dropUnique(['video_id', 'hash']);
            $table->unique('hash');
        });
    }
};
