<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'input_tokens',
        'output_tokens',
        'total_credits_deducted',
        'reserved_credits',
        'model_used',
        'type',
        'action',
        'ip_hash',
        'image_count',
        'regeneration_attempt',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
