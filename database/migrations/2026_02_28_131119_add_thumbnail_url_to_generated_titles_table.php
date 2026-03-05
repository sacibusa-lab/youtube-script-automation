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
            $table->text('thumbnail_url')->nullable()->after('mega_hook');
            $table->string('thumbnail_status')->default('pending')->after('thumbnail_url'); // pending, generating, completed, failed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_titles', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_url', 'thumbnail_status']);
        });
    }
};
