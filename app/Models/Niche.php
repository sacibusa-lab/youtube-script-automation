<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niche extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'tier',
        'description',
        'monetization_cpm',
        'rotation_weight',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function titlePatterns()
    {
        return $this->hasMany(TitlePattern::class);
    }
}
