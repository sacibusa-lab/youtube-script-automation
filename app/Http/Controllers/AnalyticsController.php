<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // 1. Overall Stats
        $usageStats = DB::table('ai_usages')
            ->where('user_id', $userId)
            ->select([
                DB::raw('SUM(input_tokens) as total_input'),
                DB::raw('SUM(output_tokens) as total_output'),
                DB::raw('SUM(credits_used) as total_credits_spent'),
                DB::raw('COUNT(*) as total_calls')
            ])
            ->first();

        // 2. Story Breakdown
        // We group by video_id to show usage per story
        $storyStats = Video::where('user_id', $userId)
            ->withCount(['generatedTitles as saved_count' => function($query) {
                $query->where('is_saved', true);
            }])
            ->get()
            ->map(function($video) {
                $usage = DB::table('ai_usages')
                    ->where('video_id', $video->id)
                    ->select([
                        DB::raw('SUM(credits_used) as credits'),
                        DB::raw('SUM(input_tokens + output_tokens) as tokens')
                    ])
                    ->first();
                
                $video->credits_used = $usage->credits ?? 0;
                $video->tokens_used = $usage->tokens ?? 0;
                return $video;
            })
            ->filter(fn($v) => $v->tokens_used > 0)
            ->values();

        return view('user.analytics', [
            'user' => $user,
            'stats' => $usageStats,
            'storyStats' => $storyStats,
            'creditBalance' => $user->total_credits - $user->used_credits
        ]);
    }
}
