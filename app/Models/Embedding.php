<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'video_id',
        'embedding_vector',
    ];

    protected $casts = [
        'embedding_vector' => 'array',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
