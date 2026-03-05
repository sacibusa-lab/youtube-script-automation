<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentStructure extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'prompt_template',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
