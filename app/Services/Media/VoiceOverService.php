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
        $this->defaultVoice = config('services.kokoro.default_voice', 'af_heart');
    }

    /**
     * Generate speech for a scene.
     *
     * @param Scene $scene
     * @param string|null $voiceId
     * @param array $options Fine-tune options (speed, volume, etc.)
     * @return string|null The relative path to the generated audio file.
     */
    public function generate(Scene $scene, ?string $voiceId = null, array $options = []): ?string
    {
        $text = $scene->narration_text;
        if (empty($text)) {
            return null;
        }

        $voice = $voiceId ?: $this->defaultVoice;
        $speed = $options['speed'] ?? 1.0;
        $volume = $options['volume'] ?? 1.0;
        
        // Generate a unique filename based on content hash, voice and options
        $hash = md5($text . $voice . $speed . $volume);
        $fileName = "scene_{$scene->id}_{$hash}.mp3";
        $folder = "audio/scenes";
        $relativePath = "{$folder}/{$fileName}";

        // If file already exists, just return it (caching)
        if (Storage::disk('public')->exists($relativePath)) {
            $scene->update([
                'audio_path' => $relativePath,
                'voice_id' => $voice
            ]);
            return $relativePath;
        }

        // Local Python Bridge implementation
        try {
            Log::info("Generating Local Kokoro voice-over via Bridge for Scene #{$scene->id}", ['voice' => $voice]);

            $bridgePath = base_path('app/Services/Media/kokoro_bridge.py');
            $modelPath = storage_path('app/models/kokoro/kokoro-v0_19.onnx');
            $voicesPath = storage_path('app/models/kokoro/voices.bin');
            $outputPath = storage_path('app/public/' . $relativePath);
            
            // Ensure directory exists
            if (!file_exists(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0777, true);
            }

            // check if python or python3 is available
            $python = "python";
            if (PHP_OS_FAMILY !== 'Windows') {
                $python = "python3"; // Common for Linux/Ubuntu servers
            }

            $result = \Illuminate\Support\Facades\Process::run([
                $python,
                $bridgePath,
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

            // Extract JSON from output (it might contain debug info before the JSON string)
            $jsonStart = strpos($output, '{');
            $jsonEnd = strrpos($output, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonContent = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
                $data = json_decode($jsonContent, true);
            } else {
                $data = null;
            }

            if ($data && isset($data['success']) && $data['success']) {
                $scene->update([
                    'audio_path' => $relativePath,
                    'voice_id' => $voice
                ]);

                return $relativePath;
            }

            Log::error("Local Kokoro Bridge Data Processing Failed", [
                'error' => $data['error'] ?? 'No JSON result found',
                'raw_output' => $output
            ]);

        } catch (\Error $e) {
            // Specifically catch "Call to undefined function" which is an Error in PHP 7+
            if (str_contains($e->getMessage(), 'shell_exec') || str_contains($e->getMessage(), 'proc_open')) {
                Log::error("CRITICAL: PHP execution functions (proc_open/shell_exec) are disabled on this server. Please enable them in php.ini to use Kokoro TTS.");
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
