<?php

namespace App\Services\AI;

use App\Services\AI\DTO\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate response from AI provider
     *
     * @param string $prompt
     * @param array $options
     * @return AIResponse
     * @throws \App\Services\AI\Exceptions\ProviderException
     */
    public function generate(string $prompt, array $options = []): AIResponse;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getName(): string;
}
