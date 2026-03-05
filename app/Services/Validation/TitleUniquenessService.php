<?php

namespace App\Services\Validation;

use App\Models\GeneratedTitle;
use Illuminate\Support\Str;

class TitleUniquenessService
{
    /**
     * Validate a list of titles and return only unique ones.
     * Checks against database and locally within the list.
     *
     * @param array $titles
     * @return array
     */
    public function validateTitleVariations(array $concepts, ?int $excludeVideoId = null): array
    {
        $uniqueConcepts = [];

        foreach ($concepts as $concept) {
            // degradation: if concept is string (old format), handle it
            $title = is_array($concept) ? ($concept['title'] ?? '') : $concept;
            
            if (empty($title)) continue;

            if ($this->isUnique($title, $excludeVideoId)) {
                 // specific check if title already in uniqueConcepts
                 $alreadyInList = false;
                 foreach ($uniqueConcepts as $uc) {
                     $ucTitle = is_array($uc) ? $uc['title'] : $uc;
                     if (strcasecmp($ucTitle, $title) === 0) {
                         $alreadyInList = true;
                         break;
                     }
                 }
                 
                 if (!$alreadyInList) {
                     $uniqueConcepts[] = $concept;
                 }
            }
        }

        return $uniqueConcepts;
    }

    /**
     * Check if a title exists in the database.
     *
     * @param string $title
     * @param int|null $excludeVideoId
     * @return bool
     */
    public function isUnique(string $title, ?int $excludeVideoId = null): bool
    {
        // Normalize title for comparison (lowercase, simplify spaces)
        $normalized = Str::slug($title);
        $hash = md5($normalized);

        // Check hash existence, excluding the current video if requested
        $query = GeneratedTitle::where('hash', $hash);
        
        if ($excludeVideoId) {
            $query->where('video_id', '!=', $excludeVideoId);
        }

        return !$query->exists();
    }

    /**
     * Store valid unique titles for a video.
     *
     * @param int $videoId
     * @param array $concepts
     * @return void
     */
    public function storeTitles(int $videoId, array $concepts): void
    {
        foreach ($concepts as $concept) {
            $title = is_array($concept) ? $concept['title'] : $concept;
            $normalized = \Illuminate\Support\Str::slug($title);
            $hash = md5($normalized);

            // Extract mega_hook and thumbnail_concept from the new array structure if present
            $megaHook = is_array($concept) ? ($concept['mega_hook'] ?? ($concept['megaHooks'][0] ?? null)) : null;
            $thumbnailConcept = is_array($concept) ? ($concept['thumbnail_concept'] ?? ($concept['thumbnailConcepts'][0]['prompt'] ?? null)) : null;

            $generatedTitle = GeneratedTitle::updateOrCreate(
                [
                    'video_id' => $videoId,
                    'hash' => $hash
                ],
                [
                    'title' => $title,
                    'is_selected' => false,
                    'metadata' => is_array($concept) ? $concept : null,
                    'visual_prompt_data' => is_array($concept) ? ($concept['thumbnail_prompt_data'] ?? null) : null,
                    'thumbnail_concept' => $thumbnailConcept,
                    'mega_hook' => $megaHook,
                ]
            );

            // Dispatch image generation if prompt exists and status is pending or failed
            if ($thumbnailConcept && ($generatedTitle->thumbnail_status === 'pending' || $generatedTitle->thumbnail_status === 'failed')) {
                \App\Jobs\GenerateThumbnailImageJob::dispatch($generatedTitle);
            }
        }
    }
}
