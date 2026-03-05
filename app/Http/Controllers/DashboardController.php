<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the user-specific dashboard.
     */
    public function index()
    {
        $userId = Auth::id();

        // Personal stats
        $totalStories = Video::where('user_id', $userId)->count();
        $generatingCount = Video::where('user_id', $userId)->whereIn('status', ['pending', 'generating_structure', 'generating_chapters', 'generating_scenes'])->count();
        $completedCount = Video::where('user_id', $userId)->where('status', 'completed')->count();

        // AI Usage
        $totalTokens = DB::table('ai_usages')
            ->where('user_id', $userId)
            ->sum(DB::raw('input_tokens + output_tokens'));

        // Recent personal projects
        $recentProjects = Video::where('user_id', $userId)
            ->latest()
            ->limit(4)
            ->get();
            
        // Billing & Credits
        $user = Auth::user();
        $plan = $user->plan;
        
        $creditsUsedThisMonth = (float) $user->credits_used_this_month;
        $creditsRemaining = max(0, $user->total_credits - $user->used_credits);
        
        $averageTokensPerScript = 0;
        if ($totalStories > 0) {
            $averageTokensPerScript = $totalTokens / $totalStories;
        } else {
            // Default baseline if no stories
            $averageTokensPerScript = 8000;
        }
        
        $estimatedScriptsRemaining = $averageTokensPerScript > 0 ? floor($creditsRemaining / $averageTokensPerScript) : 0;
        
        $usagePercentage = 0;
        if ($plan && $plan->monthly_credits > 0) {
            $usagePercentage = ($creditsUsedThisMonth / $plan->monthly_credits) * 100;
        } elseif ($user->total_credits > 0) {
           $usagePercentage = ($user->used_credits / $user->total_credits) * 100;
        }

        return view('dashboard', compact(
            'totalStories',
            'generatingCount',
            'completedCount',
            'totalTokens',
            'recentProjects',
            'creditsUsedThisMonth',
            'creditsRemaining',
            'estimatedScriptsRemaining',
            'averageTokensPerScript',
            'usagePercentage',
            'plan'
        ));
    }
}
