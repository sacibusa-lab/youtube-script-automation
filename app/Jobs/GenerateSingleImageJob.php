<?php

namespace App\Jobs;

use App\Models\Scene;
use App\Services\BillingService;
use App\Services\Media\ImageService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class GenerateSingleImageJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected Scene $scene;
    protected ?string $provider;
    protected int $regenerationAttempt;

    public function __construct(Scene $scene, ?string $provider, int $regenerationAttempt = 0)
    {
        $this->scene               = $scene;
        $this->provider            = $provider;
        $this->regenerationAttempt = $regenerationAttempt;
    }

    public function handle(ImageService $imageService, BillingService $billing): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $user = $this->scene->video->user;

        // ── BILLING: Validate and deduct image credits before generation ──────
        $ipHash  = hash('sha256', Request::ip() ?? '');
        $result  = $billing->deductImageCredits(
            user:                 $user,
            imageCount:           1,
            regenerationAttempt:  $this->regenerationAttempt,
            modelUsed:            $this->provider ?? 'image-provider',
            ipHash:               $ipHash
        );

        if (!$result['success']) {
            Log::error("[BILLING] Image generation blocked for scene {$this->scene->id}: {$result['message']}");
            $this->scene->update(['image_status' => 'blocked', 'image_error' => $result['message']]);
            // Do NOT throw — just stop this scene gracefully
            return;
        }
        // ─────────────────────────────────────────────────────────────────────

        try {
            Log::info("Generating image for scene ID: {$this->scene->id} using {$this->provider}");

            $imageUrl = $imageService->generateImage(
                $this->scene->video->user_id,
                $this->provider,
                $this->scene,
                $this->scene->character_references ?? [],
                'scenes'
            );

            if ($imageUrl) {
                $this->scene->update([
                    'image_url'      => $imageUrl,
                    'image_provider' => $this->provider
                ]);
            } else {
                throw new \Exception("Image service returned null URL for scene {$this->scene->id}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to generate image for scene ID: {$this->scene->id}", ['error' => $e->getMessage()]);
            throw $e; // Throwing will trigger retry and log failure
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
        return $this->scene->video->user_id;
    }
}
