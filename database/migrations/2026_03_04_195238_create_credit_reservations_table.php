<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('reserved_amount', 15, 2)->comment('How many credits were pre-reserved');
            $table->decimal('settled_amount', 15, 2)->default(0)->comment('Actual credits used after generation');
            $table->string('status')->default('pending')->comment('pending | settled | cancelled');
            $table->string('model_used')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_reservations');
    }
};
