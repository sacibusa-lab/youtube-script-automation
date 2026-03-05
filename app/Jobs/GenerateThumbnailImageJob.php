<?php

namespace App\Jobs;

use App\Models\GeneratedTitle;
use App\Services\BillingService;
use App\Services\Media\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class GenerateThumbnailImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected GeneratedTitle $generatedTitle;
    protected int $regenerationAttempt;

    public function __construct(GeneratedTitle $generatedTitle, int $regenerationAttempt = 0)
    {
        $this->generatedTitle      = $generatedTitle;
        $this->regenerationAttempt = $regenerationAttempt;
    }

    public function handle(ImageService $imageService, BillingService $billing): void
    {
        Log::info("Starting thumbnail image generation for title ID: {$this->generatedTitle->id}");

        $this->generatedTitle->update(['thumbnail_status' => 'generating']);

        // 1. Resolve Image Provider via refactored ImageService
        $provider = $imageService->resolveProvider($this->generatedTitle->video->user_id);
        $user     = $this->generatedTitle->video->user;

        // ── BILLING: Deduct image credits before generation ───────────────────
        $ipHash = hash('sha256', Request::ip() ?? '');
        $result = $billing->deductImageCredits(
            user:                $user,
            imageCount:          1,
            regenerationAttempt: $this->regenerationAttempt,
            modelUsed:           $provider ?? 'image-provider',
            ipHash:              $ipHash
        );

        if (!$result['success']) {
            Log::error("[BILLING] Thumbnail generation blocked for title {$this->generatedTitle->id}: {$result['message']}");
            $this->generatedTitle->update(['thumbnail_status' => 'blocked']);
            return;
        }
        // ─────────────────────────────────────────────────────────────────────

        $prompt = $this->generatedTitle->thumbnail_concept;

        try {
            $imageUrl = $imageService->generateImage(
                $this->generatedTitle->video->user_id,
                $provider,
                $prompt,
                [],
                'thumbnails'
            );

            if ($imageUrl) {
                $this->generatedTitle->update([
                    'thumbnail_url'    => $imageUrl,
                    'thumbnail_status' => 'completed'
                ]);
                Log::info("Thumbnail image generation completed for title ID: {$this->generatedTitle->id}");
            } else {
                throw new \Exception("Image service returned null URL");
            }
        } catch (\Exception $e) {
            Log::error("Thumbnail image generation failed for title ID: {$this->generatedTitle->id}", ['error' => $e->getMessage()]);
            $this->generatedTitle->update(['thumbnail_status' => 'failed']);
            
            if ($this->attempts() < $this->tries) {
                $this->release(60);
            }
        }
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new RateLimited('images')];
    }

    /**
     * Get the user ID for rate limiting.
     */
    public function userId(): int
    {
        return $this->generatedTitle->video->user_id;
    }
}
