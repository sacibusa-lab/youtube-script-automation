<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitlePattern extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'niche_id',
        'pattern_text',
        'ctr_score',
        'used_count',
    ];

    public function niche()
    {
        return $this->belongsTo(Niche::class);
    }
}
