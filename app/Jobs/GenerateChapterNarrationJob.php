<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Video;
use App\Models\Chapter;
use App\Models\Scene;
use App\Services\AI\AIServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateChapterNarrationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    protected array $allChapters;

    public function __construct(Video $video, Chapter $chapter, array $allChapters)
    {
        $this->video = $video;
        $this->chapter = $chapter;
        $this->allChapters = $allChapters;
    }

    public function handle(\App\Services\AI\AIManager $aiManager, \App\Services\AI\PromptBuilder $promptBuilder): void
    {
        Log::info("Starting Step 2: Chapter narration for video {$this->video->id}, Chapter {$this->chapter->chapter_number}");

        try {
            $this->chapter->update(['status' => 'generating']);
            $characters = $this->video->character_profiles ?? [];

            // 1. Generate Chapter Narration (Tier PRO)
            $prompt = $promptBuilder->buildChapterNarrationPrompt(
                $this->video,
                $this->chapter,
                $this->allChapters,
                $characters,
                'PRO'
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'narration', $this->video->id);
            
            $responseData = json_decode($response->content, true);
            $narrationData = $responseData['content'] ?? $this->parseLegacyBackup($response->content);

            $scenes = [];
            foreach (($narrationData['scenes'] ?? []) as $sceneData) {
                $scene = Scene::create([
                    'video_id' => $this->video->id,
                    'chapter_id' => $this->chapter->id,
                    'scene_number' => $sceneData['scene_number'],
                    'narration_text' => $sceneData['narration_text'],
                    'character_references' => $sceneData['character_references'] ?? [],
                    'duration_seconds' => $sceneData['duration_seconds'] ?? 30,
                ]);
                $scenes[] = $scene;
            }

            // Step 3: Dispatch Scene Prompts
            GenerateScenePromptsJob::dispatch($this->video, $this->chapter, $scenes)
                ->delay(now()->addSeconds(10));

            Log::info("Chapter narration completed for video {$this->video->id}, Chapter {$this->chapter->chapter_number}");

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                Log::warning("Rate limit hit during narration. Retrying...");
                $this->release(60);
                return;
            }

            Log::error("Chapter narration failed", ['error' => $e->getMessage()]);
            $this->fail($e);
        }
    }

    protected function parseLegacyBackup(string $content): array
    {
        $cleanContent = preg_replace('/^```json\s*|\s*```$/', '', trim($content));
        $data = json_decode($cleanContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $data = json_decode($matches[0], true);
            }
        }

        return is_array($data) ? ($data['content'] ?? $data) : [];
    }
}
