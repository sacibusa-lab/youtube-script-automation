<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Niche;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Niche::updateOrCreate(
            ['name' => 'US Wealth Logic'],
            [
                'tier' => '1',
                'description' => 'Financial empathy and wealth building logic (Alicia Invests style)',
                'monetization_cpm' => 25.0,
                'rotation_weight' => 1.0,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Niche::where('name', 'US Wealth Logic')->delete();
    }
};
