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
        Schema::create('generated_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('hash')->unique(); // for quick uniqueness checks
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
            
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_titles');
    }
};
