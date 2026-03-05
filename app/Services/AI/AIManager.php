<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;
use App\Services\AI\Exceptions\ProviderException;
use App\Models\UserApiKey;
use App\Services\Integration\APIGatewayService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIManager
{
    protected array $providers = [];
    protected string $defaultProvider;
    protected bool $fallbackEnabled;
    protected APIGatewayService $gateway;

    public function __construct(APIGatewayService $gateway)
    {
        $this->gateway = $gateway;
        $this->defaultProvider = 'openrouter';
        $this->fallbackEnabled = true; // Enable fallback by default for resilience
        
        $this->providers = [
            'openrouter' => new OpenRouterProvider(),
            'together' => new TogetherAIProvider(),
        ];
    }

    public function getProvider(string $name): ?AIProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    public function generate(string $prompt, array $options = [], ?int $userId = null, string $jobType = 'generic', ?int $videoId = null): AIResponse
    {
        // 1. Determine Initial Provider
        $providerName = $options['provider'] ?? $this->gateway->getPrimaryProvider();
        
        // 2. Resolve Models from Role or options
        $explicitModels = isset($options['model']) ? [$options['model']] : [];
        $prioritizedModels = !empty($explicitModels) ? $explicitModels : $this->getModelPriority($providerName, $jobType);

        // 3. SMARTS: Force OpenRouter if model looks like a slash-based ID and we aren't already on a slash-compatible provider
        if (!empty($prioritizedModels) && str_contains($prioritizedModels[0], '/') && !in_array($providerName, ['openrouter', 'together'])) {
             $providerName = 'openrouter';
        }

        $lastException = null;

        // Try determined provider first, then others if fallback is enabled
        $providersToTry = [$providerName];
        if ($this->fallbackEnabled) {
            $allProviders = array_keys($this->providers);
            $providersToTry = array_unique(array_merge($providersToTry, $allProviders));
        }

        foreach ($providersToTry as $currentProvider) {
            $provider = $this->getProvider($currentProvider);
            if (!$provider) continue;

            $apiKeys = UserApiKey::where('provider', $currentProvider)
                ->where('is_active', true)
                ->where(function($q) use ($userId) {
                    $q->whereNull('user_id')->orWhere('user_id', $userId);
                })
                ->orderBy('priority', 'asc')
                ->get();

            if ($apiKeys->isEmpty()) {
                Log::warning("No active API keys found for provider [{$currentProvider}].");
                continue;
            }

            // Define the local models list for THIS provider attempt
            $currentModels = ($currentProvider === $providerName) 
                ? $prioritizedModels 
                : $this->getModelPriority($currentProvider, $jobType);

            foreach ($currentModels as $targetModel) {
                foreach ($apiKeys as $keyRecord) {
                    try {
                        $decryptedKey = Crypt::decryptString($keyRecord->api_key);
                        $attemptOptions = array_merge($options, [
                            'model' => $targetModel,
                            'api_key' => $decryptedKey,
                            'api_key_id' => $keyRecord->id
                        ]);

                        $response = $this->attemptGeneration($currentProvider, $prompt, $attemptOptions, $userId, $jobType, $videoId);
                        return $this->wrapResponse($response, $keyRecord, $targetModel);

                    } catch (\Exception $e) {
                        $lastException = $e;
                        $msg = $e->getMessage();

                        if (preg_match('/(400|401|403|422)/', $msg) && !str_contains($msg, '429')) {
                            continue; 
                        }

                        Log::warning("AI Attempt Failed: {$currentProvider}/{$targetModel}", ['error' => $msg]);
                    }
                }
            }
        }

        throw new ProviderException("AI Orchestration Failed: All resources exhausted. Last error: " . ($lastException ? $lastException->getMessage() : 'None'));
    }

    protected function attemptGeneration(string $providerName, string $prompt, array $options, ?int $userId, string $jobType, ?int $videoId): AIResponse
    {
        $provider = $this->providers[$providerName] ?? null;
        if (!$provider) throw new ProviderException("Provider [{$providerName}] not registered.");

        try {
            $response = $provider->generate($prompt, $options);
            $this->logUsage($response, $userId, $jobType, $videoId, $options['api_key_id'], 'SUCCESS');
            return $response;
        } catch (\Exception $e) {
            $this->logUsage(new AIResponse('', 0, 0, $providerName, $options['model'] ?? 'unknown', 0, []), $userId, $jobType, $videoId, $options['api_key_id'], 'FAIL');
            throw $e;
        }
    }

    protected function logUsage(AIResponse $response, ?int $userId, string $jobType, ?int $videoId, ?int $apiKeyId, string $status): void
    {
        try {
            $totalTokens = $response->inputTokens + $response->outputTokens;
            $creditsUsed = max(0.1, $totalTokens / 1000);

            DB::table('ai_usages')->insert([
                'user_id' => $userId,
                'video_id' => $videoId,
                'api_key_id' => $apiKeyId,
                'provider' => $response->provider,
                'model' => $response->model,
                'input_tokens' => $response->inputTokens,
                'output_tokens' => $response->outputTokens,
                'estimated_cost' => $response->estimatedCost,
                'credits_used' => $creditsUsed,
                'job_type' => $jobType,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($userId && $status === 'SUCCESS') {
                $user = \App\Models\User::find($userId);
                if ($user) $user->deductCredits($creditsUsed);
            }
        } catch (\Exception $e) {
            Log::error('AI Usage Logging Failed', ['error' => $e->getMessage()]);
        }
    }

    protected function getModelPriority(string $provider, string $jobType): array
    {
        $roleSlug = match ($jobType) {
            'regenerate_hook', 'regenerate_thumbnail', 'thumbnail_engine', 'strategies', 'concepts' => 'strategist',
            'structure', 'fallback_characters' => 'architect',
            'script', 'narration' => 'narrator',
            'visual_prompts', 'scenes' => 'artist',
            'monthly_plan' => 'discovery',
            default => null
        };

        if ($roleSlug) {
            $role = DB::table('ai_production_roles')->where('slug', $roleSlug)->where('is_active', true)->first();
            if ($role && !empty($role->selected_model)) {
                return [$role->selected_model];
            }
        }

        if ($provider === 'together') {
            return match ($jobType) {
                'script', 'narration' => ['mistralai/Mixtral-8x7B-Instruct-v0.1', 'togethercomputer/llama-2-70b-chat'],
                default => ['togethercomputer/llama-2-7b-chat', 'mistralai/Mistral-7B-Instruct-v0.2'],
            };
        }

        return match ($jobType) {
            'script', 'narration' => ['anthropic/claude-3.5-sonnet', 'deepseek/deepseek-chat', 'openai/gpt-4o'],
            default => ['openai/gpt-4o-mini', 'deepseek/deepseek-chat', 'google/gemini-pro-1.5'],
        };
    }

    protected function wrapResponse(AIResponse $response, UserApiKey $key, string $model): AIResponse
    {
        $raw = trim($response->content);
        // Robust markdown stripping
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $raw);
        $decoded = json_decode($clean, true);
        
        // If already perfectly structured, return a fresh response with the CLEANED content
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['status']) && isset($decoded['content'])) {
            return new AIResponse(
                content: $clean,
                inputTokens: $response->inputTokens,
                outputTokens: $response->outputTokens,
                provider: $response->provider,
                model: $response->model,
                estimatedCost: $response->estimatedCost,
                rawResponse: $response->rawResponse
            );
        }

        // Otherwise wrap it ourselves
        $orchestrated = [
            'status' => 'SUCCESS',
            'model_used' => $model,
            'api_key_slot' => $key->id,
            'token_estimate' => $response->inputTokens + $response->outputTokens,
            'content' => (json_last_error() === JSON_ERROR_NONE) ? $decoded : $clean
        ];

        return new AIResponse(
            content: json_encode($orchestrated, JSON_PRETTY_PRINT),
            inputTokens: $response->inputTokens,
            outputTokens: $response->outputTokens,
            provider: $response->provider,
            model: $response->model,
            estimatedCost: $response->estimatedCost,
            rawResponse: $response->rawResponse
        );
    }
}
