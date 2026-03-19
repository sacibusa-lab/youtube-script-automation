<?php

namespace App\Jobs;

use App\Models\Scene;
use App\Models\User;
use App\Services\Media\VoiceOverService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateVoiceOverJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected int $sceneId;
    protected ?string $voiceId;
    protected array $options;
    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sceneId, ?string $voiceId = null, array $options = [], int $userId)
    {
        $this->sceneId = $sceneId;
        $this->voiceId = $voiceId;
        $this->options = $options;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(VoiceOverService $voiceService): void
    {
        $scene = Scene::find($this->sceneId);
        $user = User::find($this->userId);

        if (!$scene || !$user) {
            return;
        }

        Log::info("Queued Voice Generation starting for Scene #{$scene->id}");

        try {
            $audioPath = $voiceService->generate($scene, $this->voiceId, $this->options);

            if ($audioPath) {
                // Tokens are usually deducted when the job is DISPATCHED to prevent double-charging on retries
                // or after SUCCESS. Here we assume deduction happened or will happen.
                Log::info("Queued Voice Generation SUCCESS for Scene #{$scene->id}");
            } else {
                Log::error("Queued Voice Generation FAILED for Scene #{$scene->id} (No audio path returned)");
                $this->fail(new \Exception("Voice synthesis returned empty path"));
            }
        } catch (\Exception $e) {
            Log::error("Queued Voice Generation Exception", ['message' => $e->getMessage()]);
            $this->fail($e);
        }
    }
}
