<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentStructure;
use App\Models\EmotionalTone;

class AntigravityLayersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Content Structures
        $structures = [
            ['name' => '3-Act Structure', 'desc' => 'Classic narrative arc: Setup, Confrontation, Resolution.', 'prompt' => 'Use a standard 3-Act Structure. Act 1: Introduce the world and inciting incident. Act 2: Rising action and obstacles. Act 3: Climax and resolution.'],
            ['name' => 'Countdown Format', 'desc' => 'List-based progression building intensity (e.g., Top 10).', 'prompt' => 'Structure the content as a countdown from X to 1. Start with interesting but lower-stakes items, building intensity and shock value with each step until the final reveal.'],
            ['name' => 'Investigation Format', 'desc' => 'Solving a mystery step-by-step.', 'prompt' => 'Structure the narrative as an unfolding investigation. Start with the "crime" or "question", then present clues, false leads, and expert analysis, culminating in the truth.'],
            ['name' => 'Biography Format', 'desc' => 'Life story focus: Rise, Fall, and Legacy.', 'prompt' => 'Follow a biographical structure. Begin with early life/origins, detail the rise to power/fame, cover the major conflict or downfall, and end with the lasting legacy.'],
            ['name' => 'Mystery Reveal', 'desc' => 'Slow burn leading to a major twist.', 'prompt' => 'Structure this as a mystery. Withhold key information until the end. Use foreshadowing and misdirection to keep the audience guessing before a shocking twist.'],
            ['name' => 'Moral Lesson', 'desc' => 'Parable style: Action -> Consequence -> Lesson.', 'prompt' => 'Structure the story as a moral lesson. Clearly establish the character\'s flaw or mistake, show the direct consequences of their actions, and end with the moral takeaway.'],
        ];

        foreach ($structures as $s) {
            ContentStructure::firstOrCreate(
                ['name' => $s['name']],
                ['description' => $s['desc'], 'prompt_template' => $s['prompt']]
            );
        }

        // 2. Emotional Tones
        $emotions = [
            ['name' => 'Fear', 'keywords' => 'dark, ominous, terrifying, chilling, dread, horror, nightmare'],
            ['name' => 'Curiosity', 'keywords' => 'hidden, secret, uncovered, mysterious, unknown, strange, bizarre'],
            ['name' => 'Shock', 'keywords' => 'unbelievable, sudden, explosive, jaw-dropping, unexpected, violent, chaos'],
            ['name' => 'Inspiration', 'keywords' => 'uplifting, triumphant, legendary, powerful, moving, heroic, grand'],
            ['name' => 'Anger', 'keywords' => 'unfair, corrupt, brutal, injustice, rage, betrayal, stolen, crime'],
            ['name' => 'Suspense', 'keywords' => 'tense, waiting, lurking, impending, dangerous, ticking clock, edge of seat'],
            ['name' => 'Sadness', 'keywords' => 'tragic, heartbreaking, loss, grief, lonely, forgotten, devastating'],
        ];

        foreach ($emotions as $e) {
            EmotionalTone::firstOrCreate(
                ['name' => $e['name']],
                ['keywords' => $e['keywords']]
            );
        }

        $this->command->info('Antigravity Structures & Emotions Seeded!');
    }
}
