<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Video;
use App\Models\Chapter;
use App\Services\AI\AIServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Traits\HandlesAIResponses;

class GenerateVideoStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HandlesAIResponses;

    public $tries = 5;
    public $timeout = 600;

    /**
     * Calculate backoff for retries
     */
    public function backoff()
    {
        return [60, 120, 300, 600];
    }

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(\App\Services\AI\AIManager $aiManager, \App\Services\AI\PromptBuilder $promptBuilder): void
    {
        Log::info("Starting video structure generation for video ID: {$this->video->id}");

        try {
            $this->video->update(['status' => 'architecting_chapters']);

            // Use the new enhanced prompt builder with Bible Logic
            $prompt = $promptBuilder->buildStructurePromptWithBible(
                $this->video->topic,
                $this->video->niche,
                $this->video->duration_minutes,
                $this->video->tier1_country,
                $this->video->selected_title,
                $this->video->mega_hook,
                'PRO',
                $this->video->century
            );

            $response = $aiManager->generate($prompt, [], $this->video->user_id, 'structure', $this->video->id);
            
            $content = $this->parseAIJSON($response->content);
            $structure = $content;

            // BIBLE LOGIC & VALIDATION
            $bible = $structure['bible'] ?? null;
            $characters = $bible['characters'] ?? [];

            // Validate exactly 4 characters
            if (count($characters) !== 4) {
                Log::warning("Bible validation failed: Found " . count($characters) . " characters. Triggering fallback generation.");
                
                // Trigger fallback character generation via AIContentService
                try {
                    $aiContentService = app(\App\Services\AI\AIContentService::class);
                    $fallbackData = $aiContentService->generateFallbackCharacters(
                        $this->video->selected_title,
                        $this->video->niche,
                        $this->video->tier1_country,
                        $this->video->mega_hook,
                        $this->video->user_id
                    );
                    
                    if (isset($fallbackData['characters']) && count($fallbackData['characters']) === 4) {
                        $characters = $fallbackData['characters'];
                        if (!isset($structure['bible'])) {
                            $structure['bible'] = [];
                        }
                        $structure['bible']['characters'] = $characters;
                        $bible = $structure['bible'];
                        Log::info("Fallback generation successful. Bible now has 4 characters.");
                    }
                } catch (\Exception $e) {
                    Log::error("Fallback character generation failed: " . $e->getMessage());
                    // Continue with what we have, or could fail the job here
                }
            }

            // Save high-level data
            $this->video->update([
                'bible_data' => $bible, // New JSON column
                'character_profiles' => $characters, // Backward compatibility
                'emotional_spike_map' => $structure['emotional_spike_map'] ?? [],
                'niche_template_used' => $this->video->niche, // Track which template logic was used
                'status' => 'generating_chapters'
            ]);

            // Create chapters without automatic dispatch
            $chapters = $structure['chapters'] ?? [];
            $chapterCount = count($chapters);
            
            if ($chapterCount === 0) {
                 throw new \Exception("AI failed to generate chapters in structure.");
            }

            $totalDurationSeconds = $this->video->duration_minutes * 60;
            $chapterDuration = $totalDurationSeconds / $chapterCount;

            foreach ($chapters as $index => $chapterData) {
                $startTime = (int)($index * $chapterDuration);
                $endTime = (int)(($index + 1) * $chapterDuration);
                
                Chapter::create([
                    'video_id' => $this->video->id,
                    'chapter_number' => $chapterData['chapter_number'] ?? ($index + 1),
                    'status' => 'pending', 
                    'title' => $chapterData['title'] ?? "Phase " . ($index + 1),
                    'hook_text' => $chapterData['hook_text'] ?? $chapterData['summary'] ?? "Initializing mission arc...",
                    'duration_seconds' => (int)$chapterDuration,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'concept_summary' => $chapterData['summary'] ?? $chapterData['concept_summary'] ?? null,
                ]);
            }

            // Update video status to waiting for the user to "Architect" chapters
            $this->video->update(['status' => 'waiting_for_chapters']);

            Log::info("Structure generation completed. Chapters created and waiting for manual architecture.");

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                 Log::warning("Rate limit hit during structure. Retrying...");
                 $this->release(60);
                 return;
            }

            Log::error("Structure generation failed", ['error' => $e->getMessage()]);
            $this->video->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
