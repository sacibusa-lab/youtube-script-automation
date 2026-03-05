<?php

namespace App\Services\AI;

use App\Models\Character;
use App\Models\CharacterLibrary;

class CharacterService
{
    /**
     * Create character profiles from script data and store them.
     *
     * @param int $videoId
     * @param array $profilesData
     * @return void
     */
    public function createProfilesFromScript(int $videoId, array $profilesData): void
    {
        // For project-specific characters, we might store them in the video's JSON column
        // but if we want to add them to a reusable library, we do it here.
        // The implementation plan mainly uses the 'character_profiles' JSON on the Video model
        // but let's assume we also want to populate the library or handle Logic for consistency.
        
        // This simple implementation will just return for now as the Video model stores the JSON.
        // However, if we wanted to 'hydrate' reusable characters:
        
        foreach ($profilesData as $profile) {
            // Logic to check if this is a recurring character could go here
            // For now, we assume these are generated fresh for the story
        }
    }
    
    /**
     * Get a formatted string of character visual details for prompts.
     * 
     * @param array $characterList List of character names
     * @param array $allProfiles Array of character profile objects/arrays from the video script
     * @return string
     */
    public function getVisualContext(array $characterList, array $allProfiles): string 
    {
        $context = [];
        foreach ($characterList as $name) {
            foreach ($allProfiles as $profile) {
                if (str_contains(strtolower($profile['name']), strtolower($name))) {
                    $context[] = "{$profile['name']}: {$profile['physical_description']}";
                    break;
                }
            }
        }
        return implode("; ", $context);
    }
}
