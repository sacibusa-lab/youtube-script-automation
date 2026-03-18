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
        // 1. Add voice tokens to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'total_voice_tokens')) {
                $table->integer('total_voice_tokens')->default(0)->after('used_image_tokens');
                $table->integer('used_voice_tokens')->default(0)->after('total_voice_tokens');
            }
        });

        // 2. Add voice tokens to plans
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'monthly_voice_tokens')) {
                $table->integer('monthly_voice_tokens')->default(0)->after('monthly_image_tokens');
                $table->integer('voice_token_cost')->default(50)->after('image_credit_cost'); // Default 50 tokens per scene
            }
        });

        // 3. Add voice tokens to topup_packages
        Schema::table('topup_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('topup_packages', 'voice_tokens')) {
                $table->integer('voice_tokens')->default(0)->after('credits');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_voice_tokens', 'used_voice_tokens']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['monthly_voice_tokens', 'voice_token_cost']);
        });

        Schema::table('topup_packages', function (Blueprint $table) {
            $table->dropColumn(['voice_tokens']);
        });
    }
};
