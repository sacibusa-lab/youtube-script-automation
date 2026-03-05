<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\GeneratedTitle;
use App\Services\AI\AIManager;
use App\Services\AI\PromptBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RegenerateHookJob implements ShouldQueue
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
            Log::error("GeneratedTitle ID {$this->titleId} not found. Aborting regeneration.");
            return;
        }

        try {
            $prompt = $promptBuilder->buildSingleHookPrompt(
                topic: $this->project->topic,
                niche: $this->project->niche,
                tier1Country: $this->project->tier1_country,
                title: $generatedTitle->title
            );

            $response = $aiManager->generate($prompt, [
                'response_format' => ['type' => 'json_object']
            ], $this->project->user_id, 'regenerate_hook');

            $data = json_decode($response->content, true);

            if (!empty($data['hook'])) {
                // Update the matching generated title in the DB
                $generatedTitle = GeneratedTitle::find($this->titleId);

                if ($generatedTitle) {
                    $generatedTitle->update([
                        'mega_hook' => $data['hook'],
                    ]);
                    
                    // If this title is currently selected as the active project concept, update the active project too
                    if ($this->project->selected_title === $generatedTitle->title) {
                         $this->project->update([
                             'mega_hook' => $data['hook']
                         ]);
                    }
                } else {
                     // Fallback to title string if ID not found (unlikely but safe)
                     $strategies = $this->project->strategies ?? [];
                     foreach ($strategies as &$strategy) {
                         // We don't have the title string here anymore, but we can log
                     }
                     Log::warning("GeneratedTitle ID {$this->titleId} not found during hook regeneration.");
                }
            }

        } catch (\Exception $e) {
            Log::error("Failed to regenerate hook for project {$this->project->id}: " . $e->getMessage());
        }
    }
}
