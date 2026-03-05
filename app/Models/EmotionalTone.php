<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmotionalTone extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'keywords',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
