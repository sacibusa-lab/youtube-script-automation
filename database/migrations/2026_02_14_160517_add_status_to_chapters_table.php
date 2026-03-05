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
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('chapter_number'); // pending, generating, completed, approved
            $table->longText('narration_text')->nullable()->after('status');
            $table->text('concept_summary')->nullable()->after('narration_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['status', 'narration_text', 'concept_summary']);
        });
    }
};
