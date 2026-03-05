<?php

namespace App\Services\AI;

use App\Services\Integration\APIGatewayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIContentService implements AIServiceInterface
{
    protected ?string $apiKey = null;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected APIGatewayService $gateway;

    public function __construct(APIGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Resolve API key for a specific user or fallback to config
     */
    protected function resolveApiKey(?int $userId = null): string
    {
        if ($userId) {
            $key = $this->gateway->getActiveKey($userId, 'openrouter');
            if ($key) return $key;
        }

        $configKey = config('services.openrouter.api_key');
        if ($configKey) return $configKey;

        throw new \RuntimeException('OpenRouter API key is not configured in APIGateway or .env');
    }

    public function generateStructure(
        string $topic,
        string $niche,
        int $durationMinutes,
        string $tier1Country,
        string $title,
        string $megaHook,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildStructurePromptWithBible($topic, $niche, $durationMinutes, $tier1Country, $title, $megaHook);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'structure');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Structure Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate fallback characters when initial generation fails validation
     */
    public function generateFallbackCharacters(
        string $title,
        string $niche,
        string $country,
        string $megaHook,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildFallbackCharacterPrompt($title, $niche, $country, $megaHook);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'fallback_characters');
            $data = $this->parseOrchestratedResponse($response->content);
            
            // Validate exactly 4 characters
            if (!isset($data['characters']) || count($data['characters']) !== 4) {
                Log::warning('Fallback character generation failed to produce exactly 4 characters', ['count' => count($data['characters'] ?? [])]);
            }
            
            return is_array($data) ? ($data['content'] ?? $data) : [];
        } catch (\Exception $e) {
            Log::error('Fallback Character Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * STEP 2: Generate narration for a specific chapter
     */
    public function generateChapterNarration(
        $video,
        $chapter,
        array $allChapters,
        array $characters,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildChapterNarrationPrompt($video, $chapter, $allChapters, $characters);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'narration');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Chapter Narration Failed', ['chapter' => $chapter->chapter_number, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * STEP 3: Generate visual prompts for scenes in a chapter
     */
    public function generateScenePrompts(
        array $scenes,
        array $characters,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildScenePromptPrompt($scenes, $characters);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'visual_prompts');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Scene Prompt Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * Shared JSON parser
     */
    protected function parseJsonResponse(string $content): array
    {
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Legacy/Combined (Keeping signature for reference but likely unused now)
     */
    public function generateScript(
        string $topic,
        string $niche,
        ?string $subNiche,
        int $durationMinutes,
        string $tier1Country,
        ?string $title = null,
        string $megaHook,
        ?int $userId = null
    ): array {
        throw new \Exception("DEPRECATED: Use multi-step generation methods instead.");
    }

    public function generateConcepts(
        string $topic,
        string $niche,
        string $tier1Country,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildConceptPrompt($topic, $niche, $tier1Country);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'concepts');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Concept Generation Failed', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);

            throw $e;
        }
    }

    /**
     * Generate 3 distinct strategies
     */
    public function generateStrategies(
        string $topic,
        string $niche,
        string $tier1Country,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildMultiStrategyPrompt($topic, $niche, $tier1Country);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'strategies');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Strategy Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate 30-day monthly plan
     */
    public function generateMonthlyPlan(
        string $topic,
        string $niche,
        string $tier1Country,
        ?int $userId = null
    ): array {
        $promptBuilder = app(PromptBuilder::class);
        $prompt = $promptBuilder->buildMonthlyPlanPrompt($topic, $niche, $tier1Country);
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'monthly_plan');
            return $this->parseOrchestratedResponse($response->content);
        } catch (\Exception $e) {
            Log::error('Monthly Plan Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Architect a specific chapter script
     */
    public function generateChapterScript(
        string $videoTitle,
        string $chapterTitle,
        array $hooks,
        string $niche,
        ?int $userId = null
    ): string {
        $promptBuilder = app(PromptBuilder::class);
        // We'll reuse the narration prompt but trimmed for a single chapter
        // Or create a specific builder method if needed. For now, we'll use a direct prompt.
        $prompt = "Video Title: {$videoTitle}\nChapter: {$chapterTitle}\nNiche: {$niche}\nHooks: " . implode(', ', $hooks) . "\n\nWrite a deep, high-retention script for THIS CHAPTER ONLY (~300-500 words). Focus on pattern interrupts and storytelling.";
        
        $aiManager = app(AIManager::class);

        try {
            $response = $aiManager->generate($prompt, [], $userId, 'script');
            $data = $this->parseOrchestratedResponse($response->content);
            return is_array($data) ? ($data['content'] ?? json_encode($data)) : (string)$data;
        } catch (\Exception $e) {
            Log::error('Chapter Script Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Parse response wrapped in Orchestration Schema
     */
    protected function parseOrchestratedResponse(string $json): array
    {
        $data = json_decode($json, true);
        if (isset($data['content'])) {
            return is_array($data['content']) ? $data['content'] : $data;
        }
        return $data;
    }

    /**
     * Parse script response from Gemini into structured format
     *
     * @param string $content
     * @return array
     */
    protected function parseScriptResponse(string $content): array
    {
        // Expected JSON structure:
        // {
        //   "mega_hook": "...",
        //   "character_profiles": [...],
        //   "chapters": [
        //     {
        //       "chapter_number": 1,
        //       "title": "...",
        //       "hook_text": "...",
        //       "scenes": [
        //         {
        //           "scene_number": 1,
        //           "narration_text": "...",
        //           "visual_prompt": "...",
        //           "character_references": [...],
        //           "duration_seconds": 30
        //         }
        //       ]
        //     }
        //   ]
        // }

        // Try to extract JSON from markdown code blocks if present
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse script JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Parse concept response from Gemini
     *
     * @param string $content
     * @return array
     */
    protected function parseConceptResponse(string $content): array
    {
        // Extract JSON array
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $concepts = json_decode($content, true);

        if (!is_array($concepts)) {
             // Basic fallback if JSON fails - try to fix common issues or log error
             Log::warning('Failed to begin parsing concepts JSON', ['content' => $content]);
             return [];
        }

        return $concepts;
    }
}
