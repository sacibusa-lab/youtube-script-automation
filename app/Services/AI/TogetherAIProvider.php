<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;
use App\Services\AI\Exceptions\ProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TogetherAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.together.api_key') ?? '';
        $this->model = config('ai.providers.together.model', 'mistralai/Mixtral-8x7B-Instruct-v0.1');
    }

    public function generate(string $prompt, array $options = []): AIResponse
    {
        $apiKey = $options['api_key'] ?? $this->apiKey;
        $targetModel = $options['model'] ?? $this->model;

        if (empty($apiKey)) {
            throw new ProviderException("Together AI API Key is missing.");
        }

        try {
            $response = Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.together.xyz/v1/chat/completions', [
                    'model' => $targetModel,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => $options['temperature'] ?? 0.7,
                    'max_tokens' => $options['max_tokens'] ?? 2000,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                
                $promptTokens = $data['usage']['prompt_tokens'] ?? 0;
                $completionTokens = $data['usage']['completion_tokens'] ?? 0;

                return new AIResponse(
                    content: $content,
                    inputTokens: $promptTokens,
                    outputTokens: $completionTokens,
                    provider: 'together',
                    model: $targetModel,
                    estimatedCost: 0.0,
                    rawResponse: $data
                );
            }

            throw new ProviderException("Together AI API error: " . $response->body());
        } catch (\Exception $e) {
            Log::error('Together AI Generation Failed', ['error' => $e->getMessage()]);
            throw new ProviderException("Failed to connect to Together AI: " . $e->getMessage());
        }
    }

    public function getName(): string
    {
        return 'together';
    }

    /**
     * Fetch list of available models from Together AI.
     */
    public function fetchModels(?string $apiKey = null): array
    {
        $key = $apiKey ?? $this->apiKey;
        if (empty($key)) return [];

        try {
            $response = Http::timeout(30)->withToken($key)->get('https://api.together.xyz/v1/models');

            if ($response->successful()) {
                $data = $response->json();
                $models = [];
                // Together AI returns a list of objects or a nested structure
                // Assuming it's a list of objects based on OpenAI compatibility
                $items = is_array($data) ? $data : ($data['data'] ?? []);
                
                foreach ($items as $m) {
                    if (isset($m['id'])) {
                        $models[$m['id']] = $m['display_name'] ?? $m['id'];
                    }
                }
                asort($models);
                return $models;
            }
        } catch (\Exception $e) {
            Log::error('Together AI Models Fetch Failed', ['error' => $e->getMessage()]);
        }

        return [];
    }
}
