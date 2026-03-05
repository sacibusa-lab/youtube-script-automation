<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name'                      => 'Basic',
                'price'                     => 5000,
                'monthly_credits'           => 800000,
                'monthly_image_tokens'      => 0,         // image quota tracking (separate from credit cost)
                'max_tokens_per_request'    => 8000,
                'concurrent_jobs'           => 1,
                'batch_generation_limit'    => 1,
                'bulk_upload'               => false,
                'series_memory'             => false,
                'rollover_percent'          => 0,
                'api_access'                => false,
                'team_members'              => 0,
                'priority_queue'            => false,
                'direct_support'            => false,
                // Image Billing V2
                'image_credit_cost'         => 35000,    // credits per image
                'max_images_per_script'     => 5,
                'max_regeneration_attempts' => 2,
            ],
            [
                'name'                      => 'Standard',
                'price'                     => 10000,
                'monthly_credits'           => 2000000,
                'monthly_image_tokens'      => 0,
                'max_tokens_per_request'    => 12000,
                'concurrent_jobs'           => 1,
                'batch_generation_limit'    => 5,
                'bulk_upload'               => false,
                'series_memory'             => false,
                'rollover_percent'          => 0,
                'api_access'                => false,
                'team_members'              => 0,
                'priority_queue'            => false,
                'direct_support'            => false,
                // Image Billing V2
                'image_credit_cost'         => 32000,
                'max_images_per_script'     => 10,
                'max_regeneration_attempts' => 2,
            ],
            [
                'name'                      => 'Creator',
                'price'                     => 20000,
                'monthly_credits'           => 5000000,
                'monthly_image_tokens'      => 0,
                'max_tokens_per_request'    => 20000,
                'concurrent_jobs'           => 2,
                'batch_generation_limit'    => 5,
                'bulk_upload'               => true,
                'series_memory'             => true,
                'rollover_percent'          => 10,
                'api_access'                => false,
                'team_members'              => 0,
                'priority_queue'            => true,
                'direct_support'            => false,
                // Image Billing V2
                'image_credit_cost'         => 28000,
                'max_images_per_script'     => 20,
                'max_regeneration_attempts' => 2,
            ],
            [
                'name'                      => 'Agency',
                'price'                     => 100000,
                'monthly_credits'           => 30000000,
                'monthly_image_tokens'      => 0,
                'max_tokens_per_request'    => 30000,
                'concurrent_jobs'           => 5,
                'batch_generation_limit'    => 10,
                'bulk_upload'               => true,
                'series_memory'             => true,
                'rollover_percent'          => 20,
                'api_access'                => true,
                'team_members'              => 5,
                'priority_queue'            => true,
                'direct_support'            => true,
                // Image Billing V2
                'image_credit_cost'         => 25000,
                'max_images_per_script'     => 999,      // Effectively unlimited per spec
                'max_regeneration_attempts' => 2,
            ]
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
    }
}
