<?php

namespace Database\Seeders;

use App\Models\TopupPackage;
use Illuminate\Database\Seeder;

class TopupPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => '100k Script Tokens',
                'credits' => 100000,
                'price' => 2000,
                'is_active' => true,
            ],
            [
                'name' => '500k Script Tokens',
                'credits' => 500000,
                'price' => 8000,
                'is_active' => true,
            ],
            [
                'name' => '1M Script Tokens',
                'credits' => 1000000,
                'price' => 15000,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            TopupPackage::updateOrCreate(['name' => $pkg['name']], $pkg);
        }
    }
}
