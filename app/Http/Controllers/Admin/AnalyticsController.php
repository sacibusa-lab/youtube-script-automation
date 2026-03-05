<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. System Wide Totals
        $totals = DB::table('ai_usages')
            ->select([
                DB::raw('SUM(credits_used) as total_credits'),
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('SUM(input_tokens + output_tokens) as total_tokens'),
                DB::raw('COUNT(DISTINCT user_id) as active_users')
            ])
            ->first();

        // 2. Provider Breakdown
        $providerStats = DB::table('ai_usages')
            ->select([
                'provider',
                DB::raw('SUM(credits_used) as credits'),
                DB::raw('SUM(estimated_cost) as cost'),
                DB::raw('COUNT(*) as calls')
            ])
            ->groupBy('provider')
            ->get();

        // 3. User Leaderboard (Most active)
        $userLeaderboard = DB::table('ai_usages')
            ->join('users', 'ai_usages.user_id', '=', 'users.id')
            ->select([
                'users.name',
                'users.email',
                DB::raw('SUM(ai_usages.credits_used) as total_spent'),
                DB::raw('COUNT(DISTINCT ai_usages.video_id) as stories_created')
            ])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', [
            'totals' => $totals,
            'providerStats' => $providerStats,
            'userLeaderboard' => $userLeaderboard
        ]);
    }
}
