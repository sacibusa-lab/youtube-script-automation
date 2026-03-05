<?php

namespace App\Services\AI;

class ContentValidator
{
    private SystemInstructionBuilder $instructionBuilder;

    public function __construct(SystemInstructionBuilder $instructionBuilder)
    {
        $this->instructionBuilder = $instructionBuilder;
    }

    /**
     * Validate character names against forbidden list
     */
    public function validateCharacterNames(array $characters): array
    {
        $errors = [];
        $forbiddenNames = $this->instructionBuilder->getForbiddenNames();

        foreach ($characters as $index => $character) {
            $name = $character['name'] ?? '';
            
            foreach ($forbiddenNames as $forbidden) {
                if (stripos($name, $forbidden) !== false) {
                    $errors[] = [
                        'type' => 'forbidden_name',
                        'character_index' => $index,
                        'character_name' => $name,
                        'forbidden_word' => $forbidden,
                        'message' => "Character name '{$name}' contains forbidden name '{$forbidden}'"
                    ];
                }
            }
        }

        return $errors;
    }

    /**
     * Validate script content for AI slang and template patterns
     */
    public function validateScriptContent(string $script): array
    {
        $errors = [];
        $warnings = [];
        $forbiddenSlang = $this->instructionBuilder->getForbiddenAISlang();

        // Check for forbidden AI slang
        foreach ($forbiddenSlang as $slang) {
            if (stripos($script, $slang) !== false) {
                $errors[] = [
                    'type' => 'forbidden_ai_slang',
                    'word' => $slang,
                    'message' => "Script contains forbidden AI slang: '{$slang}'"
                ];
            }
        }

        // Check for template patterns (repetitive phrases)
        $templatePatterns = [
            '/what happened next/i' => 'Overused phrase: "what happened next"',
            '/little did (he|she|they) know/i' => 'Overused phrase: "little did they know"',
            '/but that was just the beginning/i' => 'Overused phrase: "but that was just the beginning"',
            '/the truth was far more/i' => 'Overused phrase: "the truth was far more"',
        ];

        foreach ($templatePatterns as $pattern => $message) {
            if (preg_match_all($pattern, $script, $matches) > 2) {
                $warnings[] = [
                    'type' => 'template_pattern',
                    'pattern' => $pattern,
                    'count' => count($matches[0]),
                    'message' => $message . ' (used ' . count($matches[0]) . ' times)'
                ];
            }
        }

        // Check for minimum scene length (should be 250-300 words)
        $scenes = explode("\n\n", $script);
        foreach ($scenes as $index => $scene) {
            $wordCount = str_word_count($scene);
            if ($wordCount > 0 && $wordCount < 200) {
                $warnings[] = [
                    'type' => 'short_scene',
                    'scene_index' => $index,
                    'word_count' => $wordCount,
                    'message' => "Scene {$index} is only {$wordCount} words (recommended: 250-300)"
                ];
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'passed' => empty($errors)
        ];
    }

    /**
     * Validate Bible structure
     */
    public function validateBibleStructure(array $bible): array
    {
        $errors = [];

        // Check character count
        $characterCount = count($bible['characters'] ?? []);
        if ($characterCount !== 4) {
            $errors[] = [
                'type' => 'invalid_character_count',
                'count' => $characterCount,
                'message' => "Bible must contain exactly 4 characters, found {$characterCount}"
            ];
        }

        // Validate each character has required fields
        $requiredFields = ['name', 'role', 'appearance', 'motive', 'backstory', 'objectives', 'roleInStory'];
        foreach ($bible['characters'] ?? [] as $index => $character) {
            foreach ($requiredFields as $field) {
                if (empty($character[$field])) {
                    $errors[] = [
                        'type' => 'missing_character_field',
                        'character_index' => $index,
                        'field' => $field,
                        'message' => "Character {$index} is missing required field: {$field}"
                    ];
                }
            }
        }

        // Check for character name uniqueness
        $names = array_column($bible['characters'] ?? [], 'name');
        if (count($names) !== count(array_unique($names))) {
            $errors[] = [
                'type' => 'duplicate_character_names',
                'message' => 'Character names must be unique'
            ];
        }

        // Validate character names against forbidden list
        $nameErrors = $this->validateCharacterNames($bible['characters'] ?? []);
        $errors = array_merge($errors, $nameErrors);

        // Check for locations
        if (empty($bible['locations'])) {
            $errors[] = [
                'type' => 'missing_locations',
                'message' => 'Bible must contain at least one location'
            ];
        }

        // Check for plot points
        if (empty($bible['plotPoints'])) {
            $errors[] = [
                'type' => 'missing_plot_points',
                'message' => 'Bible must contain at least one plot point'
            ];
        }

        return [
            'errors' => $errors,
            'passed' => empty($errors)
        ];
    }

    /**
     * Validate complete video structure
     */
    public function validateVideoStructure(array $structure): array
    {
        $errors = [];
        $warnings = [];

        // Validate Bible
        if (isset($structure['bible'])) {
            $bibleValidation = $this->validateBibleStructure($structure['bible']);
            $errors = array_merge($errors, $bibleValidation['errors']);
        } else {
            $errors[] = [
                'type' => 'missing_bible',
                'message' => 'Video structure must contain a Bible'
            ];
        }

        // Validate chapters
        if (empty($structure['chapters'])) {
            $errors[] = [
                'type' => 'missing_chapters',
                'message' => 'Video structure must contain at least one chapter'
            ];
        } else {
            foreach ($structure['chapters'] as $index => $chapter) {
                if (empty($chapter['title'])) {
                    $errors[] = [
                        'type' => 'missing_chapter_title',
                        'chapter_index' => $index,
                        'message' => "Chapter {$index} is missing a title"
                    ];
                }
                if (empty($chapter['summary'])) {
                    $errors[] = [
                        'type' => 'missing_chapter_summary',
                        'chapter_index' => $index,
                        'message' => "Chapter {$index} is missing a summary"
                    ];
                }
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'passed' => empty($errors)
        ];
    }
}
