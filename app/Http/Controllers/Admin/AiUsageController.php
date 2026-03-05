<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AiUsageController extends Controller
{
    public function index()
    {
        $stats = DB::table('ai_usages')
            ->select([
                'provider',
                DB::raw('SUM(input_tokens) as total_input'),
                DB::raw('SUM(output_tokens) as total_output'),
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            ])
            ->groupBy('provider')
            ->get();

        $dailyStats = DB::table('ai_usages')
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(input_tokens + output_tokens) as total_tokens'),
                DB::raw('SUM(estimated_cost) as daily_cost')
            ])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get();

        return view('admin.ai-usage', [
            'stats' => $stats,
            'dailyStats' => $dailyStats,
            'grandTotalTokens' => $stats->sum(fn($s) => $s->total_input + $s->total_output),
            'grandTotalCost' => $stats->sum('total_cost')
        ]);
    }
}
