<?php

namespace App\Services\Validation;

use App\Models\Video;
use Illuminate\Support\Str;

class UniquenessEnforcer
{
    /**
     * Check if the generated script is unique enough.
     * Uses SHA256 for exact match and (stub) embedding for semantic similarity.
     *
     * @param string $title
     * @param string $scriptText
     * @return array ['unique' => bool, 'reason' => string, 'action' => string|null]
     */
    public function checkUniqueness(string $title, string $scriptText): array
    {
        // 1. Generate SHA256 Hash
        $contentHash = hash('sha256', $title . $scriptText);

        // 2. Check strict duplicate
        if (Video::where('content_hash', $contentHash)->exists()) {
            return [
                'unique' => false,
                'reason' => 'Exact duplicate content detected (SHA256 match).',
                'action' => 'REGENERATE_TITLE',
                'suggestion' => 'Increase narrative contrast and rotate conflict archetype.'
            ];
        }

        // 3. Check Semantic Similarity (Stub)
        // In a real implementation, we would generate an embedding for $scriptText
        // and query a vector database (or cosine similarity on stored vectors).
        $similarityScore = $this->calculateEmbeddingSimilarity($scriptText);

        if ($similarityScore > 0.85) {
            return [
                'unique' => false,
                'reason' => "High semantic similarity detected ({$similarityScore}).",
                'action' => 'REGENERATE_TITLE',
                'suggestion' => 'Increase narrative contrast and rotate conflict archetype.'
            ];
        }

        return [
            'unique' => true,
            'content_hash' => $contentHash,
            'reason' => 'Content is unique.'
        ];
    }

    /**
     * Stub for embedding similarity calculation.
     * Needs an external AI service (OpenAI, Pinecone, pgvector).
     *
     * @param string $text
     * @return float
     */
    protected function calculateEmbeddingSimilarity(string $text): float
    {
        // TODO: Integrate actual embedding service.
        return 0.0; 
    }
}
