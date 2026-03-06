<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedTitle extends Model
{
    protected $fillable = [
        'video_id',
        'title',
        'hash',
        'is_selected',
        'is_saved',
        'metadata',
        'visual_prompt_data',
        'thumbnail_concept',
        'mega_hook',
        'short_script',
        'thumbnail_url',
        'thumbnail_status',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
        'is_saved' => 'boolean',
        'metadata' => 'array',
        'visual_prompt_data' => 'array',
        'short_script' => 'array',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
