<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Video;
use App\Models\Chapter;
use App\Models\Scene;
use App\Services\AI\AIServiceInterface;
use App\Traits\HandlesAIResponses;
use Illuminate\Support\Facades\Log;

class GenerateScenePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HandlesAIResponses;

    public $tries = 5;
    public $timeout = 600;

    /**
     * Calculate backoff for retries
     */
    public function backoff()
    {
        return [60, 120, 300, 600];
    }

    protected Video $video;
    protected Chapter $chapter;
    protected $scenes;

    public function __construct(Video $video, Chapter $chapter, $scenes)
    {
        $this->video = $video;
        $this->chapter = $chapter;
        $this->scenes = $scenes;
    }

    public function handle(\App\Services\AI\AIManager $aiManager, \App\Services\AI\PromptBuilder $promptBuilder): void 
    {
        Log::info("Starting Step 3: Scene prompts for video {$this->video->id}, Chapter {$this->chapter->chapter_number}");

        try {
            $prompt = $promptBuilder->buildScenePromptPrompt(
                $this->scenes instanceof \Illuminate\Support\Collection ? $this->scenes->toArray() : (array)$this->scenes,
                $this->video->character_profiles ?? [],
                'PRO'
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'scenes', $this->video->id);
            
            $scenePrompts = $this->parseAIJSON($response->content);
            if (isset($scenePrompts['scenes'])) {
                $scenePrompts = $scenePrompts['scenes'];
            }

            foreach (($scenePrompts ?? []) as $promptData) {
                $scene = Scene::where('video_id', $this->video->id)
                    ->where('chapter_id', $this->chapter->id)
                    ->where('scene_number', $promptData['scene_number'])
                    ->first();

                if ($scene) {
                    $jsonPrompt = $promptData['prompt_data'] ?? [];
                    $scene->update([
                        'visual_prompt' => $jsonPrompt['overall_mood'] ?? 'Cinematic shot',
                        'visual_prompt_data' => $jsonPrompt
                    ]);
                }
            }

            $this->chapter->update(['status' => 'completed']);

            Log::info("Scene prompts completed for video {$this->video->id}, Chapter {$this->chapter->id}");

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                Log::warning("Rate limit hit during scene prompting. Retrying...");
                $this->release(60);
                return;
            }

            Log::error("Scene prompt generation failed", ['error' => $e->getMessage()]);
            $this->fail($e);
        }
    }
}
