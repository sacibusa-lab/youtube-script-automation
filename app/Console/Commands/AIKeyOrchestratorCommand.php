<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AIKeyOrchestratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:orchestrate-keys';

    /**
     * The console command description.
     */
    protected $description = 'Monitor AI API Key health, rotations, and success rates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== AI Orchestration Engine: Key Health Monitor ===");
        
        $keys = \App\Models\UserApiKey::orderBy('priority', 'asc')->get();
        
        $headers = ['ID', 'Provider', 'Label', 'Active', 'Priority', 'Usage (1m)', 'Usage (24h)', 'Success Rate'];
        $rows = [];

        foreach ($keys as $key) {
            $recentUsage = \Illuminate\Support\Facades\DB::table('ai_usages')
                ->where('api_key_id', $key->id)
                ->where('created_at', '>=', now()->subMinute())
                ->count();

            $dailyUsage = \Illuminate\Support\Facades\DB::table('ai_usages')
                ->where('api_key_id', $key->id)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $successCount = \Illuminate\Support\Facades\DB::table('ai_usages')
                ->where('api_key_id', $key->id)
                ->where('created_at', '>=', now()->subDay())
                ->where('status', 'SUCCESS')
                ->count();

            $rate = $dailyUsage > 0 ? round(($successCount / $dailyUsage) * 100, 1) . '%' : 'N/A';

            $rows[] = [
                $key->id,
                $key->provider,
                $key->label ?? 'Unnamed',
                $key->is_active ? "<fg=green>YES</>" : "<fg=red>NO</>",
                $key->priority,
                $recentUsage,
                $dailyUsage,
                $rate
            ];
        }

        $this->table($headers, $rows);

        $this->info("\nRecent Failures (Last 10):");
        $failures = \Illuminate\Support\Facades\DB::table('ai_usages')
            ->where('status', 'FAIL')
            ->latest()
            ->limit(10)
            ->get();

        if ($failures->isEmpty()) {
            $this->comment("No recent failures recorded.");
        } else {
            foreach ($failures as $f) {
                $this->error("[{$f->created_at}] Key ID {$f->api_key_id} | Model {$f->model} | Job {$f->job_type}");
            }
        }
    }
}
