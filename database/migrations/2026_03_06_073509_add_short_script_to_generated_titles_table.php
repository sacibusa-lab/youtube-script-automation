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
            $table->json('short_script')->nullable()->after('mega_hook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_titles', function (Blueprint $table) {
            $table->dropColumn('short_script');
        });
    }
};
