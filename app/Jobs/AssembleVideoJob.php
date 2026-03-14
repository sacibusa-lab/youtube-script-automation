<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

use App\Models\Video;
use App\Services\Video\VideoAssemblyService;

class AssembleVideoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Video $project)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(VideoAssemblyService $assemblyService): void
    {
        try {
            $videoUrl = $assemblyService->assemble($this->project);
            
            $this->project->update([
                'status' => 'completed',
                'video_url' => $videoUrl
            ]);
            
            Log::info("Project #{$this->project->id} video successfully assembled.");
        } catch (\Exception $e) {
            Log::error("Video assembly failed for Project #{$this->project->id}: " . $e->getMessage());
            $this->project->update(['status' => 'assembly_failed']);
            throw $e;
        }
    }
}
