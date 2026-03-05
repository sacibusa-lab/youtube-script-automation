<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\Scene;
use App\Services\Media\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Bus;

class GenerateImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 1200;

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(ImageService $imageService): void
    {
        Log::info("Starting image generation orchestrator for video ID: {$this->video->id}");

        // 1. Resolve Image Provider
        $provider = $imageService->resolveProvider($this->video->user_id);

        if (!$provider) {
            Log::error("No active IMAGE RENDERER API keys found for user {$this->video->user_id}.");
            return;
        }

        // Retrieve scenes that need images
        $query = $this->video->scenes()->whereNull('image_url');
        
        if ($query->count() === 0) {
            Log::info("No pending images to generate for video ID: {$this->video->id}");
            return;
        }

        $scenes = $query->cursor();

        $jobs = [];
        foreach ($scenes as $scene) {
            $jobs[] = new GenerateSingleImageJob($scene, $provider);
        }

        $videoId = $this->video->id;

        Bus::batch($jobs)
            ->name("Image Generation for Video #{$videoId}")
            ->then(function ($batch) use ($videoId) {
                // All jobs completed successfully...
                $video = Video::find($videoId);
                if ($video) {
                    $video->update(['status' => 'images_generated']);
                    Log::info("All images generated for video ID: {$videoId}");
                }
            })
            ->catch(function ($batch, $e) use ($videoId) {
                // First batch job failure...
                Log::error("Batch image generation failed for video ID: {$videoId}", ['error' => $e->getMessage()]);
            })
            ->finally(function ($batch) use ($videoId) {
                // The batch has finished executing...
            })
            ->dispatch();
            
        Log::info("Dispatched batch of " . count($jobs) . " image generation jobs for video ID: {$videoId}");
    }
}
