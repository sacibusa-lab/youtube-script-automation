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

    protected $appends = ['audio_url', 'image_url'];

    protected $casts = [
        'character_references' => 'array',
        'visual_prompt_data' => 'array',
    ];

    public function getAudioUrlAttribute()
    {
        return $this->audio_path ? asset('storage/' . $this->audio_path) : null;
    }

    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;

        // If it's already an absolute URL (legacy), sanitize it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            // Strip old domains and redundant storage prefixes
            if (str_contains($value, '/storage/')) {
                $path = explode('/storage/', $value);
                $path = end($path); // Get the part after the last /storage/
                $path = ltrim($path, '/');
                return asset('storage/' . $path);
            }
            return $value;
        }

        // If it's a relative path, ensure it doesn't already have 'storage/' prefix
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return \Illuminate\Support\Facades\Storage::url($path);
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
