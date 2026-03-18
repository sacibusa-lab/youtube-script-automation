<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{
    protected $fillable = [
        'video_id',
        'chapter_id',
        'scene_number',
        'narration_text',
        'visual_prompt',
        'visual_prompt_data',
        'character_references',
        'locked_character_slug',
        'locked_character_name',
        'image_url',
        'image_provider',
        'duration_seconds',
        'audio_path',
        'voice_id',
    ];

    protected $appends = ['audio_url'];

    protected $casts = [
        'character_references' => 'array',
        'visual_prompt_data' => 'array',
    ];

    public function getAudioUrlAttribute()
    {
        return $this->audio_path ? asset('storage/' . $this->audio_path) : null;
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
    
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
