<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\UserApiKey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the Admin Command Center dashboard.
     */
    public function index()
    {
        // Global Stats
        $totalUsers = User::count();
        $totalStories = Video::count();
        $activeKeysCount = UserApiKey::whereNull('user_id')->where('is_active', true)->count();
        $totalSystemKeys = UserApiKey::whereNull('user_id')->count();

        // Recent Platform Activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentProjects = Video::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStories',
            'activeKeysCount',
            'totalSystemKeys',
            'recentUsers',
            'recentProjects'
        ));
    }
}
