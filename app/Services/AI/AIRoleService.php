<?php

namespace App\Services\AI;

use App\Models\AIProductionRole;
use App\Models\Video;
use App\Services\AI\DTO\AIResponse;
use Illuminate\Support\Facades\Log;

class AIRoleService
{
    protected AIManager $aiManager;
    protected PromptBuilder $promptBuilder;

    public function __construct(AIManager $aiManager, PromptBuilder $promptBuilder)
    {
        $this->aiManager = $aiManager;
        $this->promptBuilder = $promptBuilder;
    }

    /**
     * Execute a task using a specific Production Role.
     */
    public function executeRoleTask(string $roleSlug, string $taskPrompt, array $context = []): AIResponse
    {
        $role = AIProductionRole::where('slug', $roleSlug)->first();
        
        if (!$role) {
            throw new \Exception("AI Production Role [{$roleSlug}] not found.");
        }

        // Use the model selected for this role, or fallback to recommendation
        $model = $role->selected_model ?? $role->recommended_model;
        
        // Build the role-specific prompt
        $finalPrompt = $this->promptBuilder->buildRolePrompt($roleSlug, $taskPrompt, $context);

        Log::info("Executing AI Role Task", [
            'role' => $roleSlug,
            'model' => $model,
            'user_id' => $context['user_id'] ?? null
        ]);

        return $this->aiManager->generate(
            prompt: $finalPrompt,
            options: ['model' => $model, 'response_format' => ['type' => 'json_object']],
            userId: $context['user_id'] ?? null,
            jobType: "role_{$roleSlug}",
            videoId: $context['video_id'] ?? null
        );
    }
}
