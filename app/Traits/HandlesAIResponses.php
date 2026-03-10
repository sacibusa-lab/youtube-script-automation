<?php

namespace App\Traits;

trait HandlesAIResponses
{
    /**
     * Fallback for legacy or malformed responses wrapped in markdown or with extra text
     */
    protected function parseAIJSON(string $content): array
    {
        // 1. Try a direct decode first
        $data = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($data) ? ($data['content'] ?? $data) : [];
        }

        // 2. Clean markdown backticks
        $cleanContent = preg_replace('/^```json\s*|\s*```$/', '', trim($content));
        $data = json_decode($cleanContent, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($data) ? ($data['content'] ?? $data) : [];
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $data = json_decode($matches[0], true);
        }

        if (is_array($data)) {
            if (isset($data['content']) && is_array($data['content'])) {
                return $data['content'];
            }
            return $data;
        }

        return [];
    }
}
