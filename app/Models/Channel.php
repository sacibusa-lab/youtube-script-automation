<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'strategy_type',
        'hybrid_intensity',
        'risk_mode',
        'primary_niche',
        'output_frequency',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function previousTitles()
    {
        return $this->hasMany(PreviousTitle::class);
    }
}
