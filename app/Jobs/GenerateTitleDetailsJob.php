<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\GeneratedTitle;
use App\Services\AI\AIManager;
use App\Services\AI\PromptBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTitleDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected Video $video;
    protected int $titleId;

    public function __construct(Video $video, int $titleId)
    {
        $this->video = $video;
        $this->titleId = $titleId;
    }

    public function handle(
        AIManager $aiManager, 
        PromptBuilder $promptBuilder
    ): void {
        Log::info("Starting Stage 2: Concept Architecture for Video ID: {$this->video->id}, Title ID: {$this->titleId}");

        try {
            $generatedTitle = GeneratedTitle::findOrFail($this->titleId);
            
            $prompt = $promptBuilder->buildConceptArchitecturePrompt(
                $generatedTitle->title,
                $this->video->topic,
                $this->video->niche,
                $this->video->tier1_country
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'strategies', $this->video->id);
            
            $responseData = json_decode($response->content, true);
            $content = $responseData['content'] ?? $responseData;

            // Extract logic
            $megaHook = $content['mega_hook'] ?? null;
            $thumbnailConcept = $content['thumbnail_concept'] ?? null;
            $shortScript = $content['short_script'] ?? null;

            // Update GeneratedTitle with details
            $generatedTitle->update([
                'mega_hook' => $megaHook,
                'thumbnail_concept' => $thumbnailConcept,
                'short_script' => $shortScript,
            ]);

            // Update Video model for the UI preview
            $this->video->update([
                'status' => 'waiting_for_launch'
            ]);

            Log::info("Stage 2: Concept Architecture completed for Video ID: {$this->video->id}.");

        } catch (\Exception $e) {
            Log::error("Stage 2 generation failed", ['error' => $e->getMessage()]);
            $this->video->update(['status' => 'failed_stage_2']);
            $this->fail($e);
        }
    }
}
