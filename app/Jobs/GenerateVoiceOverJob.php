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

    protected int $modelId = 0;
    protected string $modelType = \App\Models\Scene::class;
    protected ?string $voiceId = null;
    protected array $options = [];
    protected int $userId = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(int $modelId, ?string $voiceId = null, array $options = [], int $userId, string $modelType = \App\Models\Scene::class)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;
        $this->voiceId = $voiceId;
        $this->options = $options;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(VoiceOverService $voiceService): void
    {
        $model = $this->modelType::find($this->modelId);
        $user = User::find($this->userId);

        if (!$model || !$user) {
            return;
        }

        $typeLabel = $model instanceof \App\Models\Scene ? "Scene" : "Hook";
        Log::info("Queued Voice Generation starting for {$typeLabel} #{$model->id}");

        try {
            // Deduct tokens here if not already handled by controller
            // For now, controllers handle deduction before dispatching to keep UI in sync
            
            $audioPath = $voiceService->generate($model, $this->voiceId, $this->options);

            if ($audioPath) {
                Log::info("Queued Voice Generation SUCCESS for {$typeLabel} #{$model->id}");
            } else {
                Log::error("Queued Voice Generation FAILED for {$typeLabel} #{$model->id} (No audio path returned)");
                $this->fail(new \Exception("Voice synthesis returned empty path"));
            }
        } catch (\Exception $e) {
            Log::error("Queued Voice Generation Exception for {$typeLabel} #{$model->id}", ['message' => $e->getMessage()]);
            $this->fail($e);
        }
    }
}
