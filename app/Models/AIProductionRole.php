<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIProductionRole extends Model
{
    protected $table = 'ai_production_roles';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'recommended_model',
        'selected_model',
        'is_active',
    ];
}
