<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_logs', function (Blueprint $table) {
            $table->string('ip_hash', 64)->nullable()->after('type')->comment('Anonymised SHA-256 of user IP');
            $table->integer('image_count')->default(0)->after('ip_hash');
            $table->decimal('reserved_credits', 15, 2)->default(0)->after('total_credits_deducted')->comment('Credits pre-reserved before generation');
            $table->integer('regeneration_attempt')->default(0)->after('image_count')->comment('Regen attempt number for this image');
        });
    }

    public function down(): void
    {
        Schema::table('credit_logs', function (Blueprint $table) {
            $table->dropColumn(['ip_hash', 'image_count', 'reserved_credits', 'regeneration_attempt']);
        });
    }
};
