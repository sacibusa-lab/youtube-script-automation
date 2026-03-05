<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\GeneratedTitle;
use App\Services\AI\AIManager;
use App\Services\AI\PromptBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RegenerateThumbnailJob implements ShouldQueue
{
    use \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, Queueable, \Illuminate\Queue\SerializesModels;

    public $timeout = 180;

    protected Video $project;
    protected int $titleId;

    /**
     * Create a new job instance.
     */
    public function __construct(Video $project, int $titleId)
    {
        $this->project = $project;
        $this->titleId = $titleId;
    }

    /**
     * Execute the job.
     */
    public function handle(AIManager $aiManager, PromptBuilder $promptBuilder): void
    {
        $generatedTitle = GeneratedTitle::find($this->titleId);
        if (!$generatedTitle) {
            Log::error("GeneratedTitle ID {$this->titleId} not found. Aborting thumbnail regeneration.");
            return;
        }

        try {
            $prompt = $promptBuilder->buildSingleThumbnailPrompt(
                topic: $this->project->topic,
                niche: $this->project->niche,
                title: $generatedTitle->title
            );

            $response = $aiManager->generate($prompt, [
                'response_format' => ['type' => 'json_object']
            ], $this->project->user_id, 'regenerate_thumbnail');

            $data = json_decode($response->content, true);

            if (!empty($data['thumbnail'])) {
                // Update the matching generated title in the DB
                $generatedTitle->update([
                    'thumbnail_concept' => $data['thumbnail'],
                    'thumbnail_url' => null, // Clear old image
                    'thumbnail_status' => 'pending' // Trigger new generation
                ]);

                // Dispatch image generation
                \App\Jobs\GenerateThumbnailImageJob::dispatch($generatedTitle);
                
                // If this title is currently selected as the active project concept, update the active project too
                if ($this->project->selected_title === $generatedTitle->title) {
                     $this->project->update([
                         'thumbnail_concept' => $data['thumbnail']
                     ]);
                }
            } else {
                 Log::warning("AI failed to return 'thumbnail' key during regeneration for ID {$this->titleId}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to regenerate thumbnail for project {$this->project->id}: " . $e->getMessage());
        }
    }
}
