<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncUserCredits extends Command
{
    protected $signature   = 'billing:sync-credits {--user= : Sync a specific user ID only}';
    protected $description = 'Sync all users credit balances to match their assigned plan allocation';

    public function handle()
    {
        $query = User::with('plan');

        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->error('No users found.');
            return;
        }

        $this->info("Syncing credit balances for {$users->count()} user(s)...");
        $this->newLine();

        $fixed = 0;
        $skipped = 0;

        foreach ($users as $user) {
            if (!$user->plan) {
                $this->line("⚠  User {$user->id} ({$user->email}) — <comment>No plan assigned, skipping</comment>");
                $skipped++;
                continue;
            }

            $user->total_credits           = $user->plan->monthly_credits;
            $user->used_credits            = 0;
            $user->credits_used_this_month = 0;
            $user->total_image_tokens      = $user->plan->monthly_image_tokens;
            $user->used_image_tokens       = 0;
            $user->save();

            $this->line("✓  User {$user->id} ({$user->email}) — <info>{$user->plan->name}</info> plan → " .
                        number_format($user->total_credits) . " script tokens, " .
                        number_format($user->total_image_tokens) . " image tokens");
            $fixed++;
        }

        $this->newLine();
        $this->info("Done. {$fixed} user(s) synced, {$skipped} skipped.");
    }
}
