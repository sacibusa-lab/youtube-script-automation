<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Character extends Model
{
    use HasFactory;

    protected $table = 'characters';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'visual_traits',
        'niche',
        'reference_image_url',
        'voice_profile_id',
        'is_global',
    ];

    protected $casts = [
        'visual_traits' => 'array',
        'is_global' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Characters available to a specific user (their own + all global ones).
     */
    public function scopeAvailableTo($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_global', true);
        });
    }

    /**
     * Only global (starter) characters.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Build a rich visual description string for AI injection.
     * This is what gets injected into the image / narration prompt.
     */
    public function buildVisualPromptString(): string
    {
        $traits = $this->visual_traits ?? [];
        $parts = [
            "Character: {$this->name}",
            $traits['age'] ?? null ? "Age: {$traits['age']}" : null,
            $traits['ethnicity'] ?? null ? "Ethnicity: {$traits['ethnicity']}" : null,
            $traits['hair'] ?? null ? "Hair: {$traits['hair']}" : null,
            $traits['eyes'] ?? null ? "Eyes: {$traits['eyes']}" : null,
            $traits['build'] ?? null ? "Build: {$traits['build']}" : null,
            $traits['style'] ?? null ? "Style: {$traits['style']}" : null,
            $traits['signature_detail'] ?? null ? "Signature Detail: {$traits['signature_detail']}" : null,
        ];

        return implode('. ', array_filter($parts)) . '.';
    }

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Character $character) {
            if (empty($character->slug)) {
                $character->slug = Str::slug($character->name) . '-' . Str::random(6);
            }
        });
    }
}
