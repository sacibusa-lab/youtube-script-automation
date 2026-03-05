<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Plan;
use App\Models\CreditLog;
use App\Models\CreditReservation;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'total_credits',
        'used_credits',
        'total_image_tokens',
        'used_image_tokens',
        'plan_id',
        'last_rollover_at',
        'credits_used_this_month',
        'daily_credits_used',
        'daily_credits_reset_at',
    ];

    public function hasCredits(float $amount, string $type = 'script'): bool
    {
        if ($type === 'image') {
            return ($this->total_image_tokens - $this->used_image_tokens) >= $amount;
        }
        return ($this->total_credits - $this->used_credits) >= $amount;
    }

    public function deductCredits(float $amount, string $type = 'script'): void
    {
        if ($type === 'image') {
            $this->used_image_tokens += $amount;
        } else {
            $this->used_credits += $amount;
            $this->credits_used_this_month += $amount; // Optionally track monthly script tokens if you want
        }
        $this->save();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'last_rollover_at'         => 'datetime',
            'daily_credits_reset_at'   => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function creditLogs(): HasMany
    {
        return $this->hasMany(CreditLog::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Helper to get total remaining tokens across all pools.
     */
    public function totalTokensRemaining(): int
    {
        return (int) ($this->total_credits - $this->used_credits);
    }

    public function creditReservations(): HasMany
    {
        return $this->hasMany(CreditReservation::class);
    }

    public function primaryApiKey(): BelongsTo
    {
        return $this->belongsTo(UserApiKey::class, 'primary_api_key_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(UserApiKey::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
