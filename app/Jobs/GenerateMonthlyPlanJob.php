<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\AI\AIManager;
use App\Services\AI\PromptBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\HandlesAIResponses;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HandlesAIResponses;

    public $tries = 3;
    public $timeout = 600;

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(
        AIManager $aiManager, 
        PromptBuilder $promptBuilder
    ): void {
        Log::info("Starting monthly plan generation for video ID: {$this->video->id}");

        try {
            // This job is typically triggered after structure is ready or as part of a "Blueprint" flow
            $prompt = $promptBuilder->buildMonthlyPlanPrompt(
                $this->video->topic ?? $this->video->selected_title ?? 'Viral Story',
                $this->video->niche,
                $this->video->tier1_country
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'monthly_plan', $this->video->id);
            
            $content = $this->parseAIJSON($response->content);

            // Update Video model with monthly plan
            $this->video->update([
                'monthly_plan' => $content,
            ]);

            Log::info("Monthly plan generation completed for video ID: {$this->video->id}");

        } catch (\Exception $e) {
            Log::error("Monthly plan generation failed", ['error' => $e->getMessage()]);
            // We don't necessarily fail the video status here as monthly plan might be non-critical
            // But we fail the job for retry
            $this->fail($e);
        }
    }
}
