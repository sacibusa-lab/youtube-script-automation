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
            // Change enum to string to allow any provider
            $table->string('provider', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_api_keys', function (Blueprint $table) {
            // Revert back to enum if needed (Note: data might be lost if not in enum list)
            $table->enum('provider', ['gemini', 'openai', 'anthropic', 'stabilityai', 'other'])->change();
        });
    }
};
