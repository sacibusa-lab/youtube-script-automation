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
        $allProjects = Video::where('user_id', $userId)->with(['scenes', 'chapters'])->get();
        $totalStories = $allProjects->count();
        $generatingCount = $allProjects->whereIn('status', ['pending', 'generating_structure', 'generating_chapters', 'generating_scenes'])->count();
        $completedCount = $allProjects->filter(fn($v) => $v->isFullyReady())->count();

        // AI Usage
        $totalTokens = DB::table('ai_usages')
            ->where('user_id', $userId)
            ->sum(DB::raw('input_tokens + output_tokens'));
        // Recent personal projects
        $recentProjects = $allProjects->sortByDesc('created_at')->take(4);
            
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

        $imageTotal = $user->total_image_tokens;
        $imageUsed = $user->used_image_tokens;
        $imageRemaining = max(0, $imageTotal - $imageUsed);
        $imagePercent = $imageTotal > 0 ? min(100, ($imageUsed / $imageTotal) * 100) : 0;

        // Voice Token Stats
        $voiceTotal = $user->total_voice_tokens;
        $voiceUsed = $user->used_voice_tokens;
        $voiceRemaining = $user->voiceTokensBalance();
        $voicePercent = $user->voiceTokensFuelPercentage();
        $voiceCostPerScene = $plan ? $plan->voice_token_cost : 50;

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
            'plan',
            'imageTotal',
            'imageUsed',
            'imageRemaining',
            'imagePercent',
            'voiceTotal',
            'voiceUsed',
            'voiceRemaining',
            'voicePercent',
            'voiceCostPerScene'
        ));
    }
}
