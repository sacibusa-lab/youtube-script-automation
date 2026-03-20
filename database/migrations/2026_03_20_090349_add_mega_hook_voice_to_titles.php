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
            $table->string('mega_hook_audio_path')->nullable()->after('mega_hook');
            $table->string('mega_hook_voice_id')->nullable()->after('mega_hook_audio_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_titles', function (Blueprint $table) {
            $table->dropColumn(['mega_hook_audio_path', 'mega_hook_voice_id']);
        });
    }
};
