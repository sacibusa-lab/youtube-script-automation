<?php

namespace App\Services\AI\DTO;

class AIResponse
{
    public function __construct(
        public readonly string $content,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
        public readonly string $provider,
        public readonly string $model,
        public readonly float $estimatedCost,
        public readonly array $rawResponse = []
    ) {}
}
