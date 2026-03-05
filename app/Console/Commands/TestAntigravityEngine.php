<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\PromptBuilder;

class TestAntigravityEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'antigravity:test {topic} {niche} {country=USA}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the full Antigravity Engine prompt pipeline.';

    /**
     * Execute the console command.
     */
    public function handle(PromptBuilder $promptBuilder)
    {
        $topic = $this->argument('topic');
        $niche = $this->argument('niche');
        $country = $this->argument('country');

        $this->info("🚀 Starting Antigravity Engine Test: {$topic} ({$niche})");

        // STAGE 1: TITLE
        $this->info("\n--- STAGE 1: TITLE ENGINE ---");
        $titlePrompt = $promptBuilder->buildTitlePrompt($niche, [], 'High', 'Safe');
        $this->line("Prompt Generated (First 500 chars):");
        $this->comment(substr($titlePrompt, 0, 500) . "...");
        
        // Mock Data for subsequent stages
        $mockTitle = "The Secret History of {$topic}";
        $mockMetadata = [
            'core_theme' => 'Discovery',
            'stakes' => 'History rewritten',
            'unique_twist' => 'It was all a simulation'
        ];
        $mockOutline = [
            'hook' => 'Imagine a world...',
            'sections' => [['section_title' => 'The Beginning', 'summary' => 'Start here.']]
        ];

        // STAGE 2: METADATA
        $this->info("\n--- STAGE 2: METADATA EXTRACTION ---");
        $metaPrompt = $promptBuilder->buildMetadataPrompt($mockTitle, 'High', 'Safe');
        $this->comment(substr($metaPrompt, 0, 500) . "...");

        // STAGE 3: OUTLINE
        $this->info("\n--- STAGE 3: OUTLINE GENERATOR ---");
        $outlinePrompt = $promptBuilder->buildOutlinePrompt($mockMetadata, 15);
        $this->comment(substr($outlinePrompt, 0, 500) . "...");

        // STAGE 4: SCRIPT
        $this->info("\n--- STAGE 4: SCRIPT GENERATOR (HEAVY) ---");
        $scriptPrompt = $promptBuilder->buildFullScriptPrompt($mockTitle, $mockMetadata, $mockOutline, 2500);
        $this->comment(substr($scriptPrompt, 0, 500) . "...");

        // STAGE 5: SCENE BREAKDOWN
        $this->info("\n--- STAGE 5: SCENE BREAKDOWN ---");
        $scenePrompt = $promptBuilder->buildSceneBreakdownPrompt("Output script text...");
        $this->comment(substr($scenePrompt, 0, 500) . "...");

        // STAGE 7: THUMBNAIL
        $this->info("\n--- STAGE 7: THUMBNAIL ENGINE ---");
        $thumbPrompt = $promptBuilder->buildThumbnailPrompt($mockTitle, 'Shock', 'Man vs Self');
        $this->comment(substr($thumbPrompt, 0, 500) . "...");

        $this->info("\n✅ Antigravity Engine Prompt Pipeline Verified!");
    }
}
