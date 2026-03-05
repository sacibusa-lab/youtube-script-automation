<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;
use App\Services\AI\Exceptions\ProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? '';
        $this->model = config('ai.providers.gemini.model', 'gemini-1.5-flash');
    }

    public function generate(string $prompt, array $options = []): AIResponse
    {
        $apiKey = $options['api_key'] ?? $this->apiKey;
        $model = $options['model'] ?? $this->model;

        if (empty($apiKey)) {
            throw new ProviderException("Gemini API Key is missing.");
        }

        try {
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [['text' => $prompt]]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? 0.7,
                        'maxOutputTokens' => $options['max_tokens'] ?? 2000,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Token counts for Gemini are usually in the response if billing is enabled
                $promptTokens = $data['usageMetadata']['promptTokenCount'] ?? 0;
                $candidatesTokens = $data['usageMetadata']['candidatesTokenCount'] ?? 0;

                return new AIResponse(
                    content: $content,
                    inputTokens: $promptTokens,
                    outputTokens: $candidatesTokens,
                    provider: 'gemini',
                    model: $model,
                    estimatedCost: 0.0,
                    rawResponse: $data
                );
            }

            throw new ProviderException("Gemini API error: " . $response->body());
        } catch (\Exception $e) {
            Log::error('Gemini Generation Failed', ['error' => $e->getMessage()]);
            throw new ProviderException("Failed to connect to Gemini: " . $e->getMessage());
        }
    }

    public function getName(): string
    {
        return 'gemini';
    }
}
