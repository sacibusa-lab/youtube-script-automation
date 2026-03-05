<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the column definition to include 'New Zealand' and 'Ireland'
        // Using string instead of enum for better compatibility when modifying
        Schema::table('videos', function (Blueprint $table) {
            $table->string('tier1_country')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->enum('tier1_country', ['USA', 'UK', 'Canada', 'Australia'])->change();
        });
    }
};
