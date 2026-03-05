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
        Schema::table('ai_usages', function (Blueprint $table) {
            $table->foreignId('video_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->decimal('credits_used', 12, 2)->default(0.00)->after('estimated_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_usages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('video_id');
            $table->dropColumn('credits_used');
        });
    }
};
