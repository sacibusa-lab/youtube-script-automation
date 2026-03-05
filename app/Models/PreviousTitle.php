<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousTitle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'channel_id',
        'title',
        'used_at',
        'hash_signature',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
