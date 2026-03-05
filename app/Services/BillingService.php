<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\CreditLog;
use App\Models\CreditReservation;
use App\Notifications\LowBalanceNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BillingService
{
    // =========================================================================
    // TEXT GENERATION BILLING: Reserve → Generate → Settle
    // =========================================================================

    /**
     * STEP 1: Pre-generation reservation.
     * Checks plan cap, checks balance, holds the estimated credits.
     * Returns a reservation_id to pass into settleTokens() after generation.
     */
    public function reserveTokens(User $user, int $estimatedTokens, string $modelUsed = '', string $action = 'generation'): array
    {
        $plan = $user->plan;

        // Enforce plan max tokens per request
        if ($plan && $estimatedTokens > $plan->max_tokens_per_request) {
            $this->triggerUpgradeSuggestion($user, 'token_cap');
            return [
                'success' => false,
                'message' => "Request exceeds the maximum allowed tokens ({$plan->max_tokens_per_request}) for your {$plan->name} plan. Please upgrade to generate larger scripts.",
                'code'    => 'TOKEN_LIMIT_EXCEEDED',
            ];
        }

        // Check balance
        $available = $user->total_credits - $user->used_credits;
        if ($available < $estimatedTokens) {
            return [
                'success' => false,
                'message' => 'Insufficient credits. Please upgrade or top up.',
                'code'    => 'INSUFFICIENT_CREDITS',
            ];
        }

        // Create reservation (temporarily lock the credits)
        $reservation = CreditReservation::create([
            'user_id'         => $user->id,
            'reserved_amount' => $estimatedTokens,
            'settled_amount'  => 0,
            'status'          => 'pending',
            'model_used'      => $modelUsed,
            'action'          => $action,
        ]);

        // Deduct the reserved amount from balance immediately to prevent double-spending
        $user->used_credits += $estimatedTokens;
        $user->save();

        Log::info("[BILLING] Reserved {$estimatedTokens} credits for User {$user->id}. Reservation #{$reservation->id}");

        return [
            'success'        => true,
            'reservation_id' => $reservation->id,
            'reserved'       => $estimatedTokens,
        ];
    }

    /**
     * STEP 2: Post-generation settlement.
     * Deducts the exact token count and refunds the unused reserved credits.
     */
    public function settleTokens(User $user, int $reservationId, int $actualInput, int $actualOutput, string $ipHash = ''): array
    {
        $reservation = CreditReservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$reservation) {
            return [
                'success' => false,
                'message' => 'Reservation not found or already settled.',
                'code'    => 'INVALID_RESERVATION',
            ];
        }

        $actualTotal = $actualInput + $actualOutput;
        $reserved    = $reservation->reserved_amount;

        // Refund unused credits (reserved - actual)
        $refund = max(0, $reserved - $actualTotal);
        $user->used_credits -= $refund;
        if ($user->used_credits < 0) $user->used_credits = 0;

        // Update monthly tracker
        $user->credits_used_this_month += $actualTotal;
        $user->save();

        // Mark reservation as settled
        $reservation->settled_amount = $actualTotal;
        $reservation->status         = 'settled';
        $reservation->save();

        $plan = $user->plan;

        // Write final credit log
        CreditLog::create([
            'user_id'                => $user->id,
            'plan_id'                => $plan?->id,
            'input_tokens'           => $actualInput,
            'output_tokens'          => $actualOutput,
            'total_credits_deducted' => $actualTotal,
            'reserved_credits'       => $reserved,
            'type'                   => 'script',
            'model_used'             => $reservation->model_used,
            'action'                 => $reservation->action,
            'ip_hash'                => $ipHash,
            'image_count'            => 0,
            'regeneration_attempt'   => 0,
        ]);

        $this->checkThresholdWarning($user);

        Log::info("[BILLING] Settled Reservation #{$reservationId} for User {$user->id}. Actual: {$actualTotal}, Refund: {$refund}");

        return [
            'success'   => true,
            'deducted'  => $actualTotal,
            'refunded'  => $refund,
            'remaining' => $user->total_credits - $user->used_credits,
        ];
    }

    /**
     * STEP 2 (Cancel): If generation fails entirely, cancel the reservation and refund all.
     */
    public function cancelReservation(User $user, int $reservationId): void
    {
        $reservation = CreditReservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($reservation) {
            // Full refund
            $user->used_credits -= $reservation->reserved_amount;
            if ($user->used_credits < 0) $user->used_credits = 0;
            $user->save();

            $reservation->status = 'cancelled';
            $reservation->save();

            Log::info("[BILLING] Cancelled Reservation #{$reservationId} for User {$user->id}. Refunded: {$reservation->reserved_amount}");
        }
    }

    // =========================================================================
    // IMAGE GENERATION BILLING: Fixed per-tier credit deduction
    // =========================================================================

    /**
     * Deducts image credits, prioritizing the image token pool.
     */
    public function deductImageCredits(
        User   $user,
        int    $imageCount,
        int    $regenerationAttempt = 0,
        string $modelUsed = 'gemini-2.5-flash-image',
        string $ipHash = ''
    ): array {
        $plan = $user->plan;

        if (!$plan) {
            return [
                'success' => false,
                'message' => 'No active subscription plan found.',
                'code'    => 'NO_PLAN',
            ];
        }

        // Validate max images per script
        if ($imageCount > $plan->max_images_per_script) {
            $this->triggerUpgradeSuggestion($user, 'image_limit');
            return [
                'success' => false,
                'message' => "Your {$plan->name} plan allows a maximum of {$plan->max_images_per_script} images per script.",
                'code'    => 'IMAGE_LIMIT_EXCEEDED',
            ];
        }

        // Validate max regeneration attempts
        if ($regenerationAttempt > $plan->max_regeneration_attempts) {
            return [
                'success' => false,
                'message' => "Maximum {$plan->max_regeneration_attempts} regeneration attempts allowed per image on your plan.",
                'code'    => 'REGEN_LIMIT_EXCEEDED',
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // NEW LOGIC: Prioritize Image Token Pool over Script Credits
        // ─────────────────────────────────────────────────────────────────────
        $availableImageTokens = $user->total_image_tokens - $user->used_image_tokens;
        $imageTokensToDeduct  = min($imageCount, $availableImageTokens);
        $remainingImagesToPay = max(0, $imageCount - $imageTokensToDeduct);

        // Calculate total credit cost (only for images NOT covered by tokens)
        $creditCost = $plan->image_credit_cost * $remainingImagesToPay;

        // Check main credit balance (only if we need a fallback)
        if ($creditCost > 0) {
            $availableCredits = $user->total_credits - $user->used_credits;
            if ($availableCredits < $creditCost) {
                return [
                    'success' => false,
                    'message' => "Insufficient script credits. You had {$imageTokensToDeduct} image tokens, but the remaining {$remainingImagesToPay} image(s) require {$creditCost} script credits.",
                    'code'    => 'INSUFFICIENT_CREDITS',
                ];
            }
        }

        // Deduct from both pools
        $user->used_image_tokens       += $imageTokensToDeduct;
        $user->used_credits            += $creditCost;
        $user->credits_used_this_month += $creditCost;
        
        $user->save();

        // Write credit log
        CreditLog::create([
            'user_id'                => $user->id,
            'plan_id'                => $plan->id,
            'input_tokens'           => 0,
            'output_tokens'          => 0,
            'total_credits_deducted' => $creditCost,
            'reserved_credits'       => 0,
            'type'                   => 'image',
            'model_used'             => $modelUsed,
            'action'                 => 'image_generation',
            'ip_hash'                => $ipHash,
            'image_count'            => $imageCount,
            'regeneration_attempt'   => $regenerationAttempt,
        ]);

        $this->checkThresholdWarning($user);

        Log::info("[BILLING] Deducted {$creditCost} script credits & {$imageTokensToDeduct} image tokens for User {$user->id}. Images: {$imageCount}");

        return [
            'success'   => true,
            'deducted'  => $creditCost,
            'remaining' => $user->total_credits - $user->used_credits,
        ];
    }

    // =========================================================================
    // LIMITS & VALIDATION
    // =========================================================================

    /**
     * Validates that a regeneration attempt is within plan limits.
     */
    public function checkRegenerationLimit(User $user, int $currentAttempt): array
    {
        $plan = $user->plan;
        $maxAttempts = $plan ? $plan->max_regeneration_attempts : 2;

        if ($currentAttempt >= $maxAttempts) {
            return [
                'allowed' => false,
                'message' => "Maximum of {$maxAttempts} regeneration attempts allowed per image.",
                'code'    => 'REGEN_LIMIT_EXCEEDED',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Checks concurrent jobs limit.
     */
    public function checkConcurrentJobsLimit(User $user, int $currentActiveJobs): bool
    {
        $plan = $user->plan;
        if (!$plan) return $currentActiveJobs < 1;
        return $currentActiveJobs < $plan->concurrent_jobs;
    }

    // =========================================================================
    // MONTHLY ROLLOVER
    // =========================================================================

    /**
     * Handles monthly reset and rollover logic for Creator/Agency plans.
     * Processes script and image tokens independently.
     */
    public function processMonthlyRollover(User $user): void
    {
        $plan = $user->plan;
        if (!$plan) return;

        // Script Tokens Rollover
        $remainingScripts     = $user->total_credits - $user->used_credits;
        $scriptRolloverAmount = 0;

        if ($plan->rollover_percent > 0 && $remainingScripts > 0) {
            $allowedScriptRollover = ($plan->rollover_percent / 100) * $plan->monthly_credits;
            $allowedScriptRollover = min($allowedScriptRollover, $plan->monthly_credits); // cap = 1 month
            $scriptRolloverAmount  = min($remainingScripts, $allowedScriptRollover);
        }

        // Image Tokens Rollover
        $remainingImages     = $user->total_image_tokens - $user->used_image_tokens;
        $imageRolloverAmount = 0;

        if ($plan->rollover_percent > 0 && $remainingImages > 0) {
            $allowedImageRollover = ($plan->rollover_percent / 100) * $plan->monthly_image_tokens;
            $allowedImageRollover = min($allowedImageRollover, $plan->monthly_image_tokens);
            $imageRolloverAmount  = min($remainingImages, $allowedImageRollover);
        }

        $user->total_credits           = $plan->monthly_credits + $scriptRolloverAmount;
        $user->used_credits            = 0;
        $user->credits_used_this_month = 0;
        $user->daily_credits_used      = 0;
        $user->daily_credits_reset_at  = now();

        $user->total_image_tokens = $plan->monthly_image_tokens + $imageRolloverAmount;
        $user->used_image_tokens  = 0;

        $user->last_rollover_at = now();
        $user->save();

        Log::info("[BILLING] Monthly rollover for User {$user->id}. Scripts rolled: {$scriptRolloverAmount}, Images rolled: {$imageRolloverAmount}");
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Checks balance amount directly (legacy support / middleware use).
     */
    public function hasEnoughCredits(User $user, int $amount, string $tokenType = 'script'): bool
    {
        return $user->hasCredits((float)$amount, $tokenType);
    }

    /**
     * Threshold warning at 5% AND upgrade suggestion at 80%.
     */
    protected function checkThresholdWarning(User $user): void
    {
        $plan = $user->plan;
        if (!$plan || $plan->monthly_credits <= 0) return;

        $used             = $user->credits_used_this_month;
        $total            = $plan->monthly_credits;
        $percentUsed      = ($used / $total) * 100;
        $remaining        = $user->total_credits - $user->used_credits;
        $percentRemaining = ($remaining / $total) * 100;

        // 80% used — suggest upgrade (send email once per threshold crossing)
        if ($percentUsed >= 80 && $percentUsed < 95) {
            $this->triggerUpgradeSuggestion($user, 'credit_usage_80');
            $user->notify(new LowBalanceNotification($remaining, $total, 'script', $plan));
            Log::info("[BILLING] User {$user->id} at {$percentUsed}% script usage. Upgrade email sent.");
        }

        // 5% remaining — urgent warning
        if ($percentRemaining <= 5.0) {
            $user->notify(new LowBalanceNotification($remaining, $total, 'script', $plan));
            Log::info("[BILLING] User {$user->id} below 5% remaining credits ({$remaining} left). Alert sent.");
        }
    }

    /**
     * Centralized place to fire upgrade nudges.
     * In production: dispatch an event or notification here.
     */
    protected function triggerUpgradeSuggestion(User $user, string $reason): void
    {
        Log::info("[BILLING] Upgrade trigger for User {$user->id}. Reason: {$reason}");
        // Dispatch notification/email event here when ready:
        // event(new UpgradeSuggested($user, $reason));
    }
}
