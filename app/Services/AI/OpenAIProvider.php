<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;
use App\Services\AI\Exceptions\ProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? '';
        $this->model = config('ai.providers.openai.model', 'gpt-4o');
    }

    public function generate(string $prompt, array $options = []): AIResponse
    {
        $apiKey = $options['api_key'] ?? $this->apiKey;
        $model = $options['model'] ?? $this->model;

        if (empty($apiKey)) {
            throw new ProviderException("OpenAI API Key is missing.");
        }

        try {
            $response = Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
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
                    provider: 'openai',
                    model: $model,
                    estimatedCost: 0.0, // Cost calculation can be added if needed
                    rawResponse: $data
                );
            }

            throw new ProviderException("OpenAI API error: " . $response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Generation Failed', ['error' => $e->getMessage()]);
            throw new ProviderException("Failed to connect to OpenAI: " . $e->getMessage());
        }
    }

    public function getName(): string
    {
        return 'openai';
    }
}
