<?php

namespace Tests\Feature;

use App\Models\Video;
use App\Jobs\GenerateVideoStructureJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\AI\AIManager;
use App\Services\AI\AIContentService;
use App\Services\AI\DTO\AIResponse;
use Mockery;

class FallbackCharacterTest extends TestCase
{
    use RefreshDatabase;

    public function test_fallback_generates_characters_when_initial_empty()
    {
        // 1. Mock AIManager for initial structure generation (returning empty characters)
        $this->mock(AIManager::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn(new AIResponse(
                    json_encode([
                        'status' => 'SUCCESS',
                        'content' => [
                            'bible' => [
                                'characters' => [], // INTENTIONALLY EMPTY to trigger fallback
                                'locations' => ['Loc 1'],
                            ],
                            'emotional_spike_map' => [],
                            'chapters' => []
                        ]
                    ]),
                    100, 100, 'openrouter', 'gpt-4o-mini', 0.01, []
                ));
        });

        // 2. Mock AIContentService for fallback generation
        // Note: AIContentService is resolved via app() in the Job, so we can mock it
        $this->mock(AIContentService::class, function ($mock) {
            $mock->shouldReceive('generateFallbackCharacters')
                ->once()
                ->andReturn([
                    'characters' => [
                        ['name' => 'Fallback 1', 'role' => 'Role 1', 'appearance' => 'App 1', 'motive' => 'Motive 1', 'backstory' => 'Back 1', 'objectives' => 'Obj 1', 'roleInStory' => 'Role 1'],
                        ['name' => 'Fallback 2', 'role' => 'Role 2', 'appearance' => 'App 2', 'motive' => 'Motive 2', 'backstory' => 'Back 2', 'objectives' => 'Obj 2', 'roleInStory' => 'Role 2'],
                        ['name' => 'Fallback 3', 'role' => 'Role 3', 'appearance' => 'App 3', 'motive' => 'Motive 3', 'backstory' => 'Back 3', 'objectives' => 'Obj 3', 'roleInStory' => 'Role 3'],
                        ['name' => 'Fallback 4', 'role' => 'Role 4', 'appearance' => 'App 4', 'motive' => 'Motive 4', 'backstory' => 'Back 4', 'objectives' => 'Obj 4', 'roleInStory' => 'Role 4'],
                    ]
                ]);
        });

        $video = Video::factory()->create([
            'niche' => 'True Crime',
            'topic' => 'Bank Heist',
            'duration_minutes' => 60,
            'tier1_country' => 'USA',
            'selected_title' => 'The Heist',
            'mega_hook' => 'A daring robbery...',
        ]);
        
        // Execute the job logic directly
        $job = new GenerateVideoStructureJob($video);
        $job->handle(
            app(AIManager::class),
            app(\App\Services\AI\PromptBuilder::class)
        );
        
        $video->refresh();
        
        // Assert that we have characters despite the initial failure
        $this->assertNotNull($video->bible_data);
        $this->assertCount(4, $video->getBibleCharacters());
        $this->assertEquals('Fallback 1', $video->getBibleCharacters()[0]['name']);
    }
}
