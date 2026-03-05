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
        'model_used',
        'action',
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
