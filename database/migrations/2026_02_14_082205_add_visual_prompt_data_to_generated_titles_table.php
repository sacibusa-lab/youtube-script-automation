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
            $table->json('visual_prompt_data')->nullable()->after('metadata');
            $table->text('thumbnail_concept')->nullable()->after('metadata');
            $table->text('mega_hook')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_titles', function (Blueprint $table) {
            $table->dropColumn(['visual_prompt_data', 'thumbnail_concept', 'mega_hook']);
        });
    }
};
