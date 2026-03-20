<?php

namespace App\Services\Media;

use App\Models\Scene;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VoiceOverService
{
    protected string $baseUrl;
    protected string $defaultVoice;

    public function __construct()
    {
        $this->baseUrl = config('services.kokoro.base_url');
        $this->defaultVoice = config('services.kokoro.default_voice', 'af_heart');
    }

    /**
     * Generate speech for a model (Scene or GeneratedTitle).
     *
     * @param object $model
     * @param string|null $voiceId
     * @param array $options Fine-tune options (speed, volume, etc.)
     * @return string|null The relative path to the generated audio file.
     */
    public function generate(object $model, ?string $voiceId = null, array $options = []): ?string
    {
        $isScene = $model instanceof \App\Models\Scene;
        $isTitle = $model instanceof \App\Models\GeneratedTitle;

        if (!$isScene && !$isTitle) {
            Log::error("VoiceOverService: Unsupported model type " . get_class($model));
            return null;
        }

        $text = $isScene ? $model->narration_text : $model->mega_hook;
        
        if (empty($text)) {
            return null;
        }

        $voice = $voiceId ?: $this->defaultVoice;
        $speed = $options['speed'] ?? 1.0;
        $volume = $options['volume'] ?? 1.0;
        
        // Generate a unique filename based on content hash, voice and options
        $hash = md5($text . $voice . $speed . $volume);
        $prefix = $isScene ? 'scene' : 'hook';
        $fileName = "{$prefix}_{$model->id}_{$hash}.mp3";
        $folder = $isScene ? "audio/scenes" : "audio/hooks";
        $relativePath = "{$folder}/{$fileName}";

        $updatePayload = $isScene 
            ? ['audio_path' => $relativePath, 'voice_id' => $voice]
            : ['mega_hook_audio_path' => $relativePath, 'mega_hook_voice_id' => $voice];

        // If file already exists, just return it (caching)
        if (Storage::disk('public')->exists($relativePath)) {
            $model->update($updatePayload);
            return $relativePath;
        }

        // 1. API Mode (Bypasses shell_exec restrictions)
        if (!empty($this->baseUrl)) {
            try {
                Log::info("Generating Kokoro voice-over via API for " . ($isScene ? "Scene" : "Hook") . " #{$model->id}", ['url' => $this->baseUrl]);
                
                $url = rtrim($this->baseUrl, '/');
                if (!str_ends_with($url, '/v1')) {
                    $url .= '/v1';
                }
                
                $response = Http::timeout(60)->post($url . '/audio/speech', [
                    'model' => 'kokoro',
                    'input' => $text,
                    'voice' => $voice,
                    'speed' => $speed
                ]);

                if ($response->successful()) {
                    Storage::disk('public')->put($relativePath, $response->body());
                    $model->update($updatePayload);
                    return $relativePath;
                }

                Log::error("Kokoro API Failed", ['status' => $response->status(), 'body' => $response->body()]);
            } catch (\Exception $e) {
                Log::error("Kokoro API Exception", ['message' => $e->getMessage()]);
            }
        }

        // 2. Local Python Bridge (Standard local setup)
        try {
            Log::info("Generating Local Kokoro voice-over via Bridge for " . ($isScene ? "Scene" : "Hook") . " #{$model->id}", ['voice' => $voice]);

            $bridgePath = base_path('app/Services/Media/kokoro_bridge.py');
            $modelPath = storage_path('app/models/kokoro/kokoro-v0_19.onnx');
            $voicesPath = storage_path('app/models/kokoro/voices.bin');
            $outputPath = storage_path('app/public/' . $relativePath);
            
            // Ensure directory exists
            if (!file_exists(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0777, true);
            }

            // Detect python binary (Prioritize local venv)
            $python = base_path('venv/bin/python');
            if (PHP_OS_FAMILY === 'Windows') {
                $python = base_path('venv/Scripts/python.exe');
            }

            if (!file_exists($python)) {
                $python = (PHP_OS_FAMILY !== 'Windows') ? "python3" : "python";
            }

            $result = \Illuminate\Support\Facades\Process::run([
                $python, $bridgePath,
                '--text', $text,
                '--voice', $voice,
                '--output', $outputPath,
                '--model', $modelPath,
                '--voices_bin', $voicesPath,
                '--speed', (string)$speed,
                '--volume', (string)$volume
            ]);

            $output = $result->output();
            
            if (!$result->successful()) {
                Log::error("Local Kokoro Bridge Failed", [
                    'error' => $result->errorOutput(),
                    'exit_code' => $result->exitCode(),
                    'output' => $output
                ]);
                return null;
            }

            // Parse result
            $jsonStart = strpos($output, '{');
            $jsonEnd = strrpos($output, '}');
            $data = ($jsonStart !== false && $jsonEnd !== false) 
                ? json_decode(substr($output, $jsonStart, $jsonEnd - $jsonStart + 1), true) 
                : null;

            if ($data && isset($data['success']) && $data['success']) {
                $model->update($updatePayload);
                return $relativePath;
            }

            Log::error("Local Kokoro Bridge Data Processing Failed", ['raw_output' => $output]);

        } catch (\Error $e) {
            if (str_contains($e->getMessage(), 'shell_exec') || str_contains($e->getMessage(), 'proc_open')) {
                Log::error("CRITICAL: PHP execution (proc_open) is disabled. Please enable it in php.ini OR run the standalone Kokoro API server.");
            } else {
                Log::error("Local Kokoro Bridge Error", ['message' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            Log::error("Local Kokoro Bridge Exception", ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get available voices.
     *
     * @return array
     */
    public function getAvailableVoices(): array
    {
        return [
            ['id' => 'af_heart', 'name' => 'Heart (Female)'],
            ['id' => 'af_nicole', 'name' => 'Nicole (Female)'],
            ['id' => 'af_sky', 'name' => 'Sky (Female)'],
            ['id' => 'am_adam', 'name' => 'Adam (Male)'],
            ['id' => 'am_michael', 'name' => 'Michael (Male)'],
        ];
    }
}
