<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditReservation extends Model
{
    protected $fillable = [
        'user_id',
        'reserved_amount',
        'settled_amount',
        'status',
        'model_used',
        'action',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSettled(): bool
    {
        return $this->status === 'settled';
    }
}
