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

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute($value)
    {
        if (!$value) return null;

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            if (str_contains($value, '/storage/')) {
                $path = explode('/storage/', $value);
                $path = end($path);
                $path = ltrim($path, '/');
                return asset('storage/' . $path);
            }
            return $value;
        }

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
}
