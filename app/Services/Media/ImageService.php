<?php

namespace App\Services\Media;

use App\Services\Integration\APIGatewayService;
use App\Services\AI\PromptBuilder;
use App\Services\AI\CharacterService;
use App\Models\Scene;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageService
{
    protected APIGatewayService $gateway;
    protected PromptBuilder $promptBuilder;
    protected CharacterService $characterService;

    public function __construct(APIGatewayService $gateway, PromptBuilder $promptBuilder, CharacterService $characterService)
    {
        $this->gateway = $gateway;
        $this->promptBuilder = $promptBuilder;
        $this->characterService = $characterService;
    }

    /**
     * Resolve which provider has an active API key for this user or globally.
     */
    public function resolveProvider(int $userId): ?string
    {
        $providers = ['openrouter', 'openai', 'stabilityai', 'gemini'];

        foreach ($providers as $p) {
            $hasKey = \App\Models\UserApiKey::where('is_active', true)
                ->where('provider', $p)
                ->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->exists();

            if ($hasKey) return $p;
        }

        return null;
    }

    /**
     * Get the active API key for a provider (user-specific or global).
     */
    public function getApiKey(int $userId, string $provider): ?string
    {
        $apiKey = $this->gateway->getActiveKey($userId, $provider);

        if (!$apiKey) {
            $keyRecord = \App\Models\UserApiKey::whereNull('user_id')
                ->where('provider', $provider)
                ->where('is_active', true)
                ->first();
            if ($keyRecord) {
                $apiKey = \Illuminate\Support\Facades\Crypt::decryptString($keyRecord->api_key);
            }
        }

        return $apiKey;
    }

    /**
     * Generate an image for a specific scene or text prompt using the user's configured provider.
     *
     * @param int $userId
     * @param string|null $provider
     * @param mixed $sceneOrPrompt Either a Scene model, a GeneratedTitle model, or a plain text string.
     * @param array $allCharacterProfiles An array of character profiles from the Video model to enforce consistency.
     * @param string $folder
     * @return string|null The URL of the generated image
     */
    public function generateImage(int $userId, ?string $provider, mixed $sceneOrPrompt, array $allCharacterProfiles = [], string $folder = 'generated'): ?string
    {
        if (!$provider) {
            $provider = $this->resolveProvider($userId);
        }

        if (!$provider) {
            Log::error("No active image provider found for user {$userId}");
            return null;
        }

        $apiKey = $this->getApiKey($userId, $provider);
        if (!$apiKey) {
            Log::error("No active API key found for provider {$provider} (user {$userId})");
            return null;
        }

        // 1. Resolve the base text prompt
        if ($sceneOrPrompt instanceof \App\Models\Scene) {
            $prompt = $this->promptBuilder->buildImagePrompt($sceneOrPrompt, $sceneOrPrompt->character_references ?? []);

            // Auto-inject Global Character Library DNA based on names found in narration
            $libraryContext = $this->characterService->autoDetectCharactersFromNarration(
                $userId,
                $sceneOrPrompt->narration_text ?? ''
            );
            if ($libraryContext) {
                $prompt .= $libraryContext;
                Log::info("ImageService: Character Library DNA injected for scene #{$sceneOrPrompt->scene_number}");
            }
        }
        // If it's a GeneratedTitle model, fetch the thumbnail config
        elseif ($sceneOrPrompt instanceof \App\Models\GeneratedTitle) {
            $prompt = $sceneOrPrompt->thumbnail_concept ?? 'Cinematic youtube thumbnail';
        }
        else {
            $prompt = is_string($sceneOrPrompt) ? $sceneOrPrompt : 'Cinematic 8k shot';
        }

        // 2. Systemic 16:9 Enforcement for YouTube parity
        if (!str_contains(strtolower($prompt), '16:9') && !str_contains(strtolower($prompt), 'aspect ratio')) {
            $prompt = "[ASPECT RATIO: 16:9] WIDESCREEN HORIZONTAL. " . $prompt . " --ar 16:9";
        }

        // 3. Inject Strict Character Consistency profiles into the final prompt
        if (!empty($allCharacterProfiles)) {
            // If it's a Scene with specific character_references, use those
            if ($sceneOrPrompt instanceof \App\Models\Scene && !empty($sceneOrPrompt->character_references)) {
                $enforcementText = $this->promptBuilder->buildCharacterConsistencyText($sceneOrPrompt->character_references, $allCharacterProfiles);
            } else {
                // Otherwise, scan the prompt text for names and inject matches
                $enforcementText = $this->promptBuilder->buildCharacterConsistencyTextFromPrompt($prompt, $allCharacterProfiles);
            }
            $prompt .= $enforcementText;
        }

        try {
            return match ($provider) {
                'openai' => $this->generateOpenAIImage($apiKey, $prompt),
                'gemini' => $this->generateGeminiImage($apiKey, $prompt, $folder),
                'stabilityai' => $this->generateStabilityImage($apiKey, $prompt, $folder),
                'openrouter' => $this->generateOpenRouterImage($apiKey, $prompt, $folder),
                default => null
            };
        } catch (\Exception $e) {
            Log::error("Image generation failed for provider {$provider}: " . $e->getMessage());
            return null;
        }
    }

    protected function generateOpenAIImage(string $apiKey, string $prompt, string $folder = 'generated'): ?string
    {
        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1792x1024', // DALL-E 3 widescreen
                'quality' => 'hd',
                'style' => 'vivid'
            ]);

        if ($response->successful()) {
            $url = $response->json('data.0.url');
            if (!empty($response->json('data.0.b64_json'))) {
                return $this->saveBase64Image($response->json('data.0.b64_json'), $folder);
            }
            if ($url) {
                return $this->saveRemoteImage($url, $folder);
            }
            return null;
        }

        throw new \Exception('OpenAI API Error: ' . $response->body());
    }

    protected function generateStabilityImage(string $apiKey, string $prompt, string $folder = 'generated'): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->post('https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image', [
            'text_prompts' => [
                ['text' => $prompt, 'weight' => 1],
            ],
            'cfg_scale' => 7,
            'height' => 768,
            'width' => 1344, // 16:9 ratio for SDXL
            'samples' => 1,
            'steps' => 30,
        ]);

        if ($response->successful()) {
            $base64Image = $response->json('artifacts.0.base64');
            return $this->saveBase64Image($base64Image, $folder);
        }

        throw new \Exception('Stability AI API Error: ' . $response->body());
    }
    
    // Placeholder for Gemini Igen if available, or fallback
    protected function generateGeminiImage(string $apiKey, string $prompt, string $folder = 'generated'): ?string
    {
        try {
            // Using Imagen 3 via Gemini API (v1beta)
            $response = Http::timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/imagen-3:predict?key={$apiKey}", [
                    'instances' => [
                        ['prompt' => $prompt]
                    ],
                    'parameters' => [
                        'sampleCount' => 1,
                        'aspectRatio' => '16:9'
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                // Imagen returns base64 in predictions[0].bytesBase64Encoded
                $base64 = $data['predictions'][0]['bytesBase64Encoded'] ?? null;
                
                if ($base64) {
                    return $this->saveBase64Image($base64, $folder);
                }
            }

            throw new \Exception('Gemini (Imagen) API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Gemini Image Generation Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate image via OpenRouter using the chat completions endpoint.
     */
    protected function generateOpenRouterImage(string $apiKey, string $prompt, string $folder = 'generated'): ?string
    {
        // Get the configured Visual Artist model
        $configuredModel = \App\Models\AIProductionRole::where('slug', 'artist')
            ->value('selected_model');

        // Detect image-capable models by keyword patterns (instead of a brittle whitelist)
        // Any model with flux, dall-e, imagen, stable-diffusion, or 'image' in the name is treated as image-capable
        $imageKeywords = ['flux', 'dall-e', 'imagen', 'stable-diffusion', 'sdxl', 'image'];
        $isImageModel = false;
        if ($configuredModel) {
            foreach ($imageKeywords as $keyword) {
                if (stripos($configuredModel, $keyword) !== false) {
                    $isImageModel = true;
                    break;
                }
            }
        }

        if ($isImageModel) {
            $imageModel = $configuredModel;
        } else {
            // Fallback to a valid OpenRouter image model
            $imageModel = 'black-forest-labs/flux-1-schnell';
            Log::info("Visual Artist role model '{$configuredModel}' does not appear to be an image model. Falling back to Flux Schnell.");
        }

        Log::info("OpenRouter Image Generation | Model: {$imageModel} | Prompt: " . substr($prompt, 0, 80));

        // OpenRouter: ALL generation goes through /chat/completions — including image models
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $imageModel,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                // Add explicit dimensional hints for models that support parameter mapping on OpenRouter
                'width' => 1344,
                'height' => 768,
                'aspect_ratio' => '16:9',
                'aspectRatio' => '16:9',
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $rawMessage = $data['choices'][0]['message'] ?? [];

            // Full untruncated debug log — remove once working
            Log::debug('OpenRouter image full response', [
                'model'        => $imageModel,
                'message_keys' => array_keys($rawMessage),
                'images_count' => count($rawMessage['images'] ?? []),
                'annotations_count' => count($rawMessage['annotations'] ?? []),
                'content_type' => gettype($rawMessage['content'] ?? null),
                'content'      => is_string($rawMessage['content'] ?? null)
                    ? substr($rawMessage['content'], 0, 150)
                    : '(not a string)',
                'images_preview'      => json_encode($rawMessage['images'][0] ?? 'none'),
                'annotations_preview' => json_encode($rawMessage['annotations'][0] ?? 'none'),
            ]);

            // ── 1️⃣  message.images  (Gemini via OpenRouter — primary) ──
            if (!empty($rawMessage['images']) && is_array($rawMessage['images'])) {
                foreach ($rawMessage['images'] as $img) {
                    $b64 = $img['image_base64'] ?? $img['b64_json'] ?? $img['data'] ?? null;
                    if ($b64) return $this->saveBase64Image($b64, $folder);
                    
                    // URL form (can be top-level 'url' or nested 'image_url.url')
                    $url = $img['image_url']['url'] ?? $img['url'] ?? null;
                    if ($url) {
                        if (str_starts_with($url, 'data:')) {
                            $b64 = preg_replace('#^data:[^;]+;base64,#', '', $url);
                            return $this->saveBase64Image($b64, $folder);
                        }
                        return $this->saveRemoteImage($url, $folder);
                    }
                }
            }

            // ── 2️⃣  message.annotations  (Google AI Studio variant) ──
            if (!empty($rawMessage['annotations']) && is_array($rawMessage['annotations'])) {
                foreach ($rawMessage['annotations'] as $ann) {
                    $b64 = $ann['image_base64'] ?? $ann['b64_json'] ?? $ann['data'] ?? null;
                    if ($b64) return $this->saveBase64Image($b64, $folder);

                    $url = $ann['image_url']['url'] ?? $ann['url'] ?? null;
                    if ($url) {
                        if (str_starts_with($url, 'data:')) {
                            $b64 = preg_replace('#^data:[^;]+;base64,#', '', $url);
                            return $this->saveBase64Image($b64, $folder);
                        }
                        return $this->saveRemoteImage($url, $folder);
                    }
                }
            }

            // ── 3️⃣  content as array of parts (multimodal / OpenAI vision) ──
            $content = $rawMessage['content'] ?? null;
            if (is_array($content)) {
                foreach ($content as $part) {
                    $type = $part['type'] ?? '';
                    if ($type === 'image' || $type === 'image_base64') {
                        $b64 = $part['image_base64'] ?? $part['data'] ?? $part['image_url']['url'] ?? null;
                        if ($b64 && str_starts_with($b64, 'data:')) {
                            $b64 = preg_replace('#^data:[^;]+;base64,#', '', $b64);
                        }
                        if ($b64) return $this->saveBase64Image($b64, $folder);
                    }
                    if ($type === 'image_url' || isset($part['image_url'])) {
                        $url = $part['image_url']['url'] ?? $part['url'] ?? null;
                        if ($url) {
                            if (str_starts_with($url, 'data:')) {
                                $b64 = preg_replace('#^data:[^;]+;base64,#', '', $url);
                                return $this->saveBase64Image($b64, $folder);
                            }
                            return $this->saveRemoteImage($url, $folder);
                        }
                    }
                    if ($type === 'inline_data' || isset($part['inline_data'])) {
                        $b64 = ($part['inline_data'] ?? $part)['data'] ?? null;
                        if ($b64) return $this->saveBase64Image($b64, $folder);
                    }
                }
            }

            // ── 4️⃣  content as plain string URL or Data URI ──
            if (is_string($content)) {
                $trimmed = trim($content);
                if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
                    return $this->saveRemoteImage($trimmed, $folder);
                }
                if (str_starts_with($trimmed, 'data:image/')) {
                    $b64 = preg_replace('#^data:[^;]+;base64,#', '', $trimmed);
                    return $this->saveBase64Image($b64, $folder);
                }
            }

            // ── 5️⃣  top-level data[] (DALL-E / OpenAI images endpoint style) ──
            if (!empty($data['data'][0]['url'])) {
                return $this->saveRemoteImage($data['data'][0]['url'], $folder);
            }
            if (!empty($data['data'][0]['b64_json'])) {
                return $this->saveBase64Image($data['data'][0]['b64_json'], $folder);
            }

            Log::warning('OpenRouter: 200 OK but no image extracted', [
                'model'        => $imageModel,
                'message_keys' => array_keys($rawMessage),
            ]);
        }

        Log::error('OpenRouter Image Generation Failed', [
            'model' => $imageModel,
            'status' => $response->status(),
            'body' => substr($response->body(), 0, 500),
        ]);
        return null;
    }

    protected function saveBase64Image(string $base64, string $folder = 'generated'): string
    {
        // Decode and save to public storage
        $image = base64_decode($base64);
        $filename = $folder . '/' . uniqid() . '.png';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $image);
        return \Illuminate\Support\Facades\Storage::url($filename);
    }

    /**
     * Download a remote image and save it locally.
     */
    protected function saveRemoteImage(string $url, string $folder = 'generated'): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);
            if ($response->successful()) {
                $contents = $response->body();
                $extension = 'png'; // Default
                
                // Try to guess extension from content-type
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg')) $extension = 'jpg';
                if (str_contains($contentType, 'webp')) $extension = 'webp';
                
                $filename = $folder . '/' . uniqid() . '.' . $extension;
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $contents);
                return \Illuminate\Support\Facades\Storage::url($filename);
            }
        } catch (\Exception $e) {
            Log::error("Failed to localize remote image from {$url}: " . $e->getMessage());
        }
        
        return $url; // Fallback to remote URL if download fails
    }
}
