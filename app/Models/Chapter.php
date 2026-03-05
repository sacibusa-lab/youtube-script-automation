<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'video_id',
        'chapter_number',
        'status',
        'title',
        'duration_seconds',
        'start_time',
        'end_time',
        'hook_text',
        'narration_text',
        'concept_summary',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function scenes()
    {
        return $this->hasMany(Scene::class);
    }
}
