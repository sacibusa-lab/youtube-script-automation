<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BillingService;
use Illuminate\Console\Command;

class ProcessMonthlyRollover extends Command
{
    protected $signature   = 'billing:monthly-rollover {--user= : Process a specific user ID only} {--dry-run : Preview what would happen without saving}';
    protected $description = 'Resets monthly credit balances and applies rollover for Creator/Agency plans';

    public function handle(BillingService $billing)
    {
        $isDryRun = $this->option('dry-run');

        $query = User::with('plan');

        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        $users = $query->whereNotNull('plan_id')->get();

        if ($users->isEmpty()) {
            $this->error('No users with active plans found.');
            return;
        }

        $this->info(($isDryRun ? '[DRY RUN] ' : '') . "Processing monthly rollover for {$users->count()} user(s)...");
        $this->newLine();

        foreach ($users as $user) {
            if (!$user->plan) {
                $this->line("⚠  User {$user->id} ({$user->email}) — <comment>No plan, skipping</comment>");
                continue;
            }

            $remainingScripts = $user->total_credits - $user->used_credits;
            $remainingImages  = $user->total_image_tokens - $user->used_image_tokens;
            $rollover         = $user->plan->rollover_percent;

            $scriptRollover = $rollover > 0 
                ? min($remainingScripts, ($rollover / 100) * $user->plan->monthly_credits)
                : 0;
            $imageRollover  = $rollover > 0
                ? min($remainingImages, ($rollover / 100) * $user->plan->monthly_image_tokens)
                : 0;

            $this->line(
                "✓  User {$user->id} ({$user->email}) — <info>{$user->plan->name}</info> | " .
                "Scripts: " . number_format($user->plan->monthly_credits) . " + " . number_format($scriptRollover) . " rollover | " .
                "Images: " . number_format($user->plan->monthly_image_tokens) . " + " . number_format($imageRollover) . " rollover"
            );

            if (!$isDryRun) {
                $billing->processMonthlyRollover($user);
            }
        }

        $this->newLine();
        $this->info($isDryRun ? '[DRY RUN] No changes saved.' : 'Monthly rollover complete.');
    }
}
