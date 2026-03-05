<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chapter;
use App\Models\Scene;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel_id',
        'niche_id',
        'content_structure_id',
        'emotional_tone_id',
        'topic',
        'niche',
        'sub_niche',
        'tier1_country',
        'duration_minutes',
        'chapter_count',
        'status',
        'selected_title',
        'selected_title_index',
        'script',
        'script_text',
        'metadata',
        'outline',
        'scene_json',
        'image_prompt_json',
        'thumbnail_json',
        'character_profiles',
        'mega_hook',
        'title_variations',
        'emotional_spike_map',
        'similarity_score',
        'hash_signature',
        'monetization_tier',
        'word_count',
        'thumbnail_visual_prompt_data',
        'thumbnail_concept',
        'bible_data',
        'niche_template_used',
        'diverse_name_pool',
        'century',
        'specific_year',
        'strategies',
        'monthly_plan',
        'platform_data',
    ];

    protected $casts = [
        'script' => 'array',
        'character_profiles' => 'array',
        'title_variations' => 'array',
        'tags' => 'array',
        'emotional_spike_map' => 'array',
        'bible_data' => 'array',
        'diverse_name_pool' => 'array',
        'metadata' => 'array',
        'outline' => 'array',
        'scene_json' => 'array',
        'image_prompt_json' => 'array',
        'thumbnail_json' => 'array',
        'thumbnail_visual_prompt_data' => 'array',
        'strategies' => 'array',
        'monthly_plan' => 'array',
        'platform_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function niche()
    {
        return $this->belongsTo(Niche::class);
    }

    public function contentStructure()
    {
        return $this->belongsTo(ContentStructure::class);
    }

    public function emotionalTone()
    {
        return $this->belongsTo(EmotionalTone::class);
    }

    public function embedding()
    {
        return $this->hasOne(Embedding::class);
    }

    public function generationQueue()
    {
        return $this->hasMany(GenerationQueue::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function scenes()
    {
        return $this->hasMany(Scene::class);
    }

    public function generatedTitles()
    {
        return $this->hasMany(GeneratedTitle::class);
    }

    // Bible Data Helpers
    public function getBibleCharacters()
    {
        return $this->bible_data['characters'] ?? $this->character_profiles ?? [];
    }

    public function getBibleLocations()
    {
        return $this->bible_data['locations'] ?? [];
    }

    public function getBibleKeyItems()
    {
        return $this->bible_data['keyItems'] ?? [];
    }

    public function getBiblePlotPoints()
    {
        return $this->bible_data['plotPoints'] ?? [];
    }

    public function getMonthlyPlan()
    {
        return $this->monthly_plan['content'] ?? $this->monthly_plan ?? [];
    }
}
