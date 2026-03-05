<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\ContentStructure;
use App\Models\EmotionalTone;
use App\Models\GeneratedTitle;
use App\Models\Niche;
use App\Models\User;
use App\Models\Video;
use App\Services\Validation\TitleUniquenessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThumbnailPromptTest extends TestCase
{
    use RefreshDatabase;

    public function test_concept_generation_stores_lightweight_concepts()
    {
        $user = User::factory()->create();
        $video = Video::create([
            'user_id' => $user->id,
            'topic' => 'Test Topic',
            'niche' => 'Test Niche',
            'status' => 'pending',
            'tier1_country' => 'USA',
            'duration_minutes' => 30,
        ]);

        $concepts = [
            [
                'title' => 'Viral Title 1',
                'mega_hook' => 'Mega Hook 1',
                'thumbnail_concept' => 'Cinematic thumbnail of a robot.',
            ]
        ];

        $service = app(TitleUniquenessService::class);
        $service->storeTitles($video->id, $concepts);

        $this->assertDatabaseHas('generated_titles', [
            'video_id' => $video->id,
            'title' => 'Viral Title 1',
            'thumbnail_concept' => 'Cinematic thumbnail of a robot.',
        ]);

        $generatedTitle = GeneratedTitle::where('title', 'Viral Title 1')->first();
        $this->assertNull($generatedTitle->visual_prompt_data);
    }

    public function test_selecting_title_triggers_second_stage_generation()
    {
        \Illuminate\Support\Facades\Bus::fake();

        $user = User::factory()->create();
        $channel = Channel::create(['name' => 'Test Channel', 'strategy_type' => 'Documentary']);
        $niche = Niche::create(['name' => 'Test Niche', 'tier' => '1']);
        $structure = ContentStructure::create(['name' => 'Test Structure']);
        $emotion = EmotionalTone::create(['name' => 'Test Emotion']);

        $video = Video::create([
            'user_id' => $user->id,
            'channel_id' => $channel->id,
            'niche_id' => $niche->id,
            'content_structure_id' => $structure->id,
            'emotional_tone_id' => $emotion->id,
            'topic' => 'Test Topic',
            'niche' => 'Test Niche',
            'tier1_country' => 'USA',
            'duration_minutes' => 30,
            'status' => 'waiting_for_concept_selection',
        ]);

        $generatedTitle = GeneratedTitle::create([
            'video_id' => $video->id,
            'title' => 'Selected Title',
            'hash' => md5('selected-title'),
            'mega_hook' => 'Stored Mega Hook',
            'thumbnail_concept' => 'The selected thumbnail concept',
            'metadata' => ['some' => 'metadata']
        ]);

        $response = $this->actingAs($user)
            ->post(route('projects.select-title', $video), [
                'selected_title' => 'Selected Title',
                'mega_hook' => 'Overridden Hook',
            ]);

        $response->assertRedirect();
        
        $video->refresh();
        $this->assertEquals('Selected Title', $video->selected_title);
        $this->assertEquals('generating_thumbnail_concept', $video->status);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\GenerateThumbnailPromptJob::class);
    }
}
