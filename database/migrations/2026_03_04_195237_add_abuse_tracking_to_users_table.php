<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rolling daily credits counter for abuse spike detection
            $table->decimal('daily_credits_used', 15, 2)->default(0)->after('credits_used_this_month');
            // When the daily counter was last reset (midnight UTC)
            $table->timestamp('daily_credits_reset_at')->nullable()->after('daily_credits_used');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_credits_used', 'daily_credits_reset_at']);
        });
    }
};
