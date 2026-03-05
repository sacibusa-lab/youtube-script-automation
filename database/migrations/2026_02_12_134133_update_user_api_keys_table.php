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
        Schema::table('user_api_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('user_api_keys', 'label')) {
                $table->string('label')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('user_api_keys', 'priority')) {
                $table->integer('priority')->default(0)->after('is_active');
            }
            
            // Change provider to string to support more providers easily
            $table->string('provider')->change();

            // Add a non-unique index to satisfy the foreign key before dropping the unique one
            // We use a specific name to avoid collisions
            $table->index('user_id', 'user_api_keys_user_id_index');
        });

        Schema::table('user_api_keys', function (Blueprint $table) {
            // Now we can safely drop the unique constraint if it exists
            // We catch potential failure if it's already dropped
            try {
                $table->dropUnique('user_api_keys_user_id_provider_unique');
            } catch (\Exception $e) {
                // Already dropped or different name
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_api_keys', function (Blueprint $table) {
            $table->dropColumn(['label', 'priority']);
            $table->dropIndex('user_api_keys_user_id_index');
            
            // Restore unique constraint
            $table->unique(['user_id', 'provider']);
        });
    }
};
