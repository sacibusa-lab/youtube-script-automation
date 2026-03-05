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
use Illuminate\Support\Facades\Log;

class GenerateThumbnailPromptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(AIManager $aiManager, PromptBuilder $promptBuilder): void
    {
        Log::info("Starting detailed thumbnail prompt generation for video ID: {$this->video->id}");

        try {
            $this->video->update(['status' => 'generating_thumbnail_concept']);

            $prompt = $promptBuilder->buildThumbnailPrompt(
                $this->video->selected_title,
                $this->video->niche,
                'Primary conflict related to ' . $this->video->topic,
                'PRO'
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'thumbnail_engine', $this->video->id);
            
            $responseData = json_decode($response->content, true);
            $data = $responseData['content'] ?? $this->parseLegacyBackup($response->content);

            if (!$data || !isset($data['prompt_data'])) {
                throw new \Exception("AI failed to return valid detailed prompt data.");
            }

            $this->video->update([
                'thumbnail_visual_prompt_data' => $data['prompt_data'],
                'thumbnail_json' => array_merge($this->video->thumbnail_json ?? [], [
                    'text_hooks' => $data['thumbnail_text_options'] ?? [],
                    'visual_concept' => $data['visual_concept'] ?? $this->video->thumbnail_concept
                ]),
                'status' => 'generating_structure',
            ]);

            \App\Jobs\GenerateVideoStructureJob::dispatch($this->video);

            Log::info("Detailed thumbnail prompt generation completed for video ID: {$this->video->id}");

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                Log::warning("Rate limit hit during thumbnail prompt. Retrying...");
                $this->release(60); 
                return;
            }

            Log::error("Detailed thumbnail prompt generation failed", ['error' => $e->getMessage()]);
            $this->video->update(['status' => 'failed']);
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
