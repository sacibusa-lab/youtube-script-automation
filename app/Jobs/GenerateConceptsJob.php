<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\AI\AIManager;
use App\Services\AI\PromptBuilder;
use App\Services\Validation\TitleUniquenessService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Traits\HandlesAIResponses;

class GenerateConceptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HandlesAIResponses;

    public $tries = 5;
    public $timeout = 600;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff()
    {
        return [60, 120, 300, 600]; // Exponential backoff to handle 429s (1m, 2m, 5m, 10m)
    }

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(
        AIManager $aiManager, 
        PromptBuilder $promptBuilder
    ): void {
        Log::info("Starting multi-strategy generation for video ID: {$this->video->id}");

        try {
            $this->video->update(['status' => 'generating_concepts']);

            $prompt = $promptBuilder->buildMultiStrategyPrompt(
                $this->video->topic ?? 'Trending Story',
                $this->video->niche,
                $this->video->tier1_country
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'strategies', $this->video->id);
            
            $content = $this->parseAIJSON($response->content);
            
            // Comprehensive Strategy Extraction Logic
            if (is_array($content)) {
                // Try strategies, then titles, then flat array
                $strategies = $content['strategies'] ?? $content['titles'] ?? $content['concepts'] ?? $content;
            } else {
                $strategies = [];
            }

            // Update Video model with strategies
            $this->video->update([
                'strategies' => $strategies,
                'status' => 'waiting_for_title_selection'
            ]);

            // Persist as GeneratedTitle records for bookmarking and selection
            $titleService = app(\App\Services\Validation\TitleUniquenessService::class);
            $titleService->storeTitles($this->video->id, $strategies);

            Log::info("Multi-strategy generation completed for video ID: {$this->video->id}. " . count($strategies) . " strategies saved and persisted.");

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                Log::warning("Rate limit hit during strategy generation. Retrying...", ['error' => $e->getMessage()]);
                $this->release(60); 
                return;
            }

            Log::error("Strategy generation failed", ['error' => $e->getMessage()]);
            $this->video->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
