<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerationQueue extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'generation_queue';

    protected $fillable = [
        'video_id',
        'stage',
        'status',
        'retry_count',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
