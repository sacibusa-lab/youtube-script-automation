<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AIProductionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'slug' => 'strategist',
                'name' => 'Production Strategist',
                'description' => 'Specializes in high-CTR Titles, Retention Hooks, and Thumbnail Concepts.',
                'recommended_model' => 'openai/gpt-4o',
                'selected_model' => 'openai/gpt-4o',
            ],
            [
                'slug' => 'architect',
                'name' => 'Lead Architect',
                'description' => 'Architects Script Outlines, Character Profiles (Bible Logic), and Narrative Flow.',
                'recommended_model' => 'anthropic/claude-3.5-sonnet',
                'selected_model' => 'anthropic/claude-3.5-sonnet',
            ],
            [
                'slug' => 'narrator',
                'name' => 'Main Narrator',
                'description' => 'Writes scene-by-scene script content, narration, and character dialogue.',
                'recommended_model' => 'anthropic/claude-3.5-sonnet',
                'selected_model' => 'anthropic/claude-3.5-sonnet',
            ],
            [
                'slug' => 'artist',
                'name' => 'Visual Artist',
                'description' => 'Engineers high-fidelity AI image generation prompts for thumbnails and scenes.',
                'recommended_model' => 'openai/gpt-4o-mini',
                'selected_model' => 'openai/gpt-4o-mini',
            ],
            [
                'slug' => 'discovery',
                'name' => 'Discovery Engine',
                'description' => 'Ideates story concepts, niche trends, and viral research.',
                'recommended_model' => 'google/gemini-pro-1.5',
                'selected_model' => 'google/gemini-pro-1.5',
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\AIProductionRole::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
