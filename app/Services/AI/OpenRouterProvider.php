<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;
use App\Services\AI\Exceptions\ProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected float $inputCost;
    protected float $outputCost;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key') ?? '';
        $this->model = config('ai.providers.openrouter.model', 'openai/gpt-4o-mini');
        $this->inputCost = config('ai.providers.openrouter.input_cost_per_1k', 0.00015);
        $this->outputCost = config('ai.providers.openrouter.output_cost_per_1k', 0.0006);
    }

    public function generate(string $prompt, array $options = []): AIResponse
    {
        $apiKey = $options['api_key'] ?? $this->apiKey;
        $targetModel = $options['model'] ?? $this->model;

        if (empty($apiKey)) {
            throw new ProviderException("OpenRouter API Key is missing.");
        }

        $maxRetries = 2;
        $attempt = 0;

        while ($attempt <= $maxRetries) {
            try {
                $payload = [
                    'model' => $targetModel,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => $options['temperature'] ?? 0.9,
                    'max_tokens' => $options['max_tokens'] ?? 4000,
                ];

                $response = Http::timeout(180)
                    ->withoutVerifying()
                    ->withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'HTTP-Referer' => config('app.url'),
                        'X-Title' => config('app.name'),
                    ])->post('https://openrouter.ai/api/v1/chat/completions', $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? '';
                    
                    $inputTokens = $data['usage']['prompt_tokens'] ?? 0;
                    $outputTokens = $data['usage']['completion_tokens'] ?? 0;
                    
                    return new AIResponse(
                        content: $content,
                        inputTokens: $inputTokens,
                        outputTokens: $outputTokens,
                        provider: 'openrouter',
                        model: $data['model'] ?? $targetModel,
                        estimatedCost: 0.0,
                        rawResponse: $data
                    );
                }

                if (in_array($response->status(), [429, 500, 502, 503, 504])) {
                    $attempt++;
                    if ($attempt <= $maxRetries) {
                        sleep(pow(2, $attempt));
                        continue;
                    }
                }

                throw new ProviderException("OpenRouter API error (Status {$response->status()}): " . $response->body());

            } catch (\Exception $e) {
                if ($e instanceof ProviderException) throw $e;
                $attempt++;
                if ($attempt <= $maxRetries) {
                    sleep(pow(2, $attempt));
                    continue;
                }
                throw new ProviderException("OpenRouter connection failed: " . $e->getMessage());
            }
        }

        throw new ProviderException("OpenRouter generation failed after retries.");
    }

    public function getName(): string
    {
        return 'openrouter';
    }

    public function fetchModels(?string $apiKey = null): array
    {
        $key = $apiKey ?? $this->apiKey;
        if (empty($key)) return [];

        try {
            $response = Http::timeout(60)->withoutVerifying()->withHeaders([
                'Authorization' => "Bearer {$key}",
            ])->get('https://openrouter.ai/api/v1/models');

            if ($response->successful()) {
                $data = $response->json();
                $models = [];
                foreach ($data['data'] as $m) {
                    $models[$m['id']] = $m['name'] ?? $m['id'];
                }
                asort($models);
                return $models;
            }
        } catch (\Exception $e) {
            Log::error('OpenRouter Models Fetch Failed', ['error' => $e->getMessage()]);
        }

        return [];
    }
}
