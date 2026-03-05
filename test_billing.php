<?php

use App\Models\User;
use App\Models\Plan;
use App\Services\BillingService;

$user = User::first();
if (!$user) {
    echo "No user found to test with.\n";
    exit;
}

// Assign Basic Plan
$basicPlan = Plan::where('name', 'Basic')->first();
$user->plan_id = $basicPlan->id;
$user->total_credits = $basicPlan->monthly_credits;
$user->used_credits = 0;
$user->save();

echo "User {$user->id} assigned Basic Plan with {$user->total_credits} credits.\n";

$billing = app(BillingService::class);

// Test subtraction
echo "Attempting to deduct 5000 tokens...\n";
$result = $billing->deductTokens($user, 3000, 2000, 'gpt-4');
echo "Result: " . json_encode($result) . "\n";

$user->refresh();
echo "Used credits: {$user->used_credits}\n";

// Test plan limits (Basic is max 8000)
echo "\nAttempting to deduct 10000 tokens (Exceeds limit)...\n";
$result2 = $billing->deductTokens($user, 5000, 5000, 'gpt-4');
echo "Result: " . json_encode($result2) . "\n";

// Test Generator logic middleware or concurrent
// Assign Creator Plan for Rollover
echo "\nAssigning Creator Plan for Rollover test...\n";
$creatorPlan = Plan::where('name', 'Creator')->first();
$user->plan_id = $creatorPlan->id;
$user->total_credits = $creatorPlan->monthly_credits; // 5M
$user->used_credits = 4000000; // Used 4M, Remaining 1M
$user->save();

echo "User has " . ($user->total_credits - $user->used_credits) . " credits remaining.\n";

$user->load('plan'); // Important: Reload relationship since we changed the plan_id
$billing->processMonthlyRollover($user);
$user->refresh();

// Rollover for Creator is 10%. 10% of 5M is 500,000.
// They had 1M remaining, so rollover should be capped at 500,000.
// New total should be 5M + 500,000 = 5,500,000.
echo "After rollover, user has {$user->total_credits} total credits.\n";

echo "Tests completed.\n";

