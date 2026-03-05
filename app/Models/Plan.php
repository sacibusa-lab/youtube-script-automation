<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'monthly_credits',
        'max_tokens_per_request',
        'concurrent_jobs',
        'batch_generation_limit',
        'bulk_upload',
        'series_memory',
        'rollover_percent',
        'api_access',
        'team_members',
        'priority_queue',
        'direct_support',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'monthly_credits' => 'decimal:2',
        'rollover_percent' => 'decimal:2',
        'bulk_upload' => 'boolean',
        'series_memory' => 'boolean',
        'api_access' => 'boolean',
        'priority_queue' => 'boolean',
        'direct_support' => 'boolean',
    ];
}
