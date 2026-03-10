<?php

namespace App\Services\AI;

use App\Models\Character;
use Illuminate\Support\Facades\Log;

class CharacterService
{
    /**
     * Get character library entries available to a user (their own + global).
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCharacters(int $userId)
    {
        return Character::availableTo($userId)
            ->orderBy('is_global', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find a library character by slug or name for a given user.
     *
     * @param int $userId
     * @param string $slugOrName
     * @return Character|null
     */
    public function findCharacter(int $userId, string $slugOrName): ?Character
    {
        return Character::availableTo($userId)
            ->where(function ($q) use ($slugOrName) {
                $q->where('slug', $slugOrName)
                  ->orWhere('name', 'LIKE', '%' . $slugOrName . '%');
            })
            ->first();
    }

    /**
     * Build a visual consistency block for a set of library character IDs.
     * This is injected directly into image generation prompts to ensure character consistency.
     *
     * @param int[] $characterIds  IDs from the library to embed.
     * @return string
     */
    public function buildLibraryCharacterContext(array $characterIds): string
    {
        if (empty($characterIds)) return '';

        $characters = Character::whereIn('id', $characterIds)->get();
        if ($characters->isEmpty()) return '';

        $parts = $characters->map(fn($c) => $c->buildVisualPromptString())->toArray();

        return "\n[CHARACTER CONSISTENCY LOCK]\n" . implode("\n", $parts) . "\n[/CHARACTER CONSISTENCY LOCK]";
    }

    /**
     * Scan the narration text of a scene for known character names and return
     * their visual DNA strings automatically (fuzzy name matching).
     *
     * @param int $userId
     * @param string $narrationText
     * @return string
     */
    public function autoDetectCharactersFromNarration(int $userId, string $narrationText): string
    {
        $available = $this->getAvailableCharacters($userId);
        if ($available->isEmpty()) return '';

        $matched = [];
        foreach ($available as $character) {
            // Check if the character's first name appears in the narration
            $firstName = explode(' ', $character->name)[0];
            if (stripos($narrationText, $firstName) !== false) {
                $matched[] = $character->buildVisualPromptString();
            }
        }

        if (empty($matched)) return '';

        return "\n[CHARACTER CONSISTENCY LOCK]\n" . implode("\n", $matched) . "\n[/CHARACTER CONSISTENCY LOCK]";
    }

    /**
     * Create character profiles from AI-generated script data.
     * Stores AI-generated ad-hoc characters for in-story use (not library characters).
     *
     * @param int $videoId
     * @param array $profilesData
     * @return void
     */
    public function createProfilesFromScript(int $videoId, array $profilesData): void
    {
        // These are per-video ad-hoc characters stored on the Video model's JSON column.
        // Global Library characters are managed separately through CharacterController.
        Log::debug("CharacterService: createProfilesFromScript called", [
            'video_id' => $videoId,
            'count'    => count($profilesData),
        ]);
    }

    /**
     * Get a formatted string of character visual details for prompts (legacy support).
     *
     * @param array $characterList List of character names
     * @param array $allProfiles Array of character profile objects/arrays
     * @return string
     */
    public function getVisualContext(array $characterList, array $allProfiles): string
    {
        $context = [];
        foreach ($characterList as $name) {
            foreach ($allProfiles as $profile) {
                if (str_contains(strtolower($profile['name'] ?? ''), strtolower($name))) {
                    $context[] = "{$profile['name']}: {$profile['physical_description']}";
                    break;
                }
            }
        }
        return implode("; ", $context);
    }
}
