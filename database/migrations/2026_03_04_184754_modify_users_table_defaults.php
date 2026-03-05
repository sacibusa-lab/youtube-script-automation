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
        Schema::table('users', function (Blueprint $table) {
            // Change default fallback from 5000 to 0
            $table->decimal('total_credits', 15, 2)->default(0.00)->change();
            
            // Add new image tokens columns
            $table->decimal('total_image_tokens', 15, 2)->default(0.00)->after('used_credits');
            $table->decimal('used_image_tokens', 15, 2)->default(0.00)->after('total_image_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert default if rolled back
            $table->decimal('total_credits', 15, 2)->default(5000.00)->change();
            
            $table->dropColumn(['total_image_tokens', 'used_image_tokens']);
        });
    }
};
