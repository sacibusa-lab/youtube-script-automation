<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Chapter;
use App\Models\Scene;
use App\Services\Media\VoiceOverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceGenerationController extends Controller
{
    /**
     * Display the dedicated Voice Generation Studio.
     */
    public function index()
    {
        $projects = Video::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'assembling', 'assembly_failed'])
            ->with(['chapters.scenes'])
            ->latest()
            ->get();

        return view('voice-generation.index', compact('projects'));
    }

    /**
     * Generate or regenerate voice for a specific scene (Queued).
     */
    public function generate(Request $request)
    {
        $request->validate([
            'scene_id' => 'required|exists:scenes,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $scene = Scene::with('chapter.video')->findOrFail($request->scene_id);
        
        if ($scene->chapter->video->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $cost = $user->plan->voice_token_cost ?? 50;

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        // Deduct Tokens upfront (Standard practice for background jobs to prevent abuse)
        if (!$user->isAdmin()) {
            $user->deductCredits($cost, 'voice');
        }

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        // Dispatch Job
        \App\Jobs\GenerateVoiceOverJob::dispatch(
            $scene->id,
            $request->voice_id,
            $options,
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Voice generation started in background',
            'scene_id' => $scene->id,
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Check the status of a voice generation.
     */
    public function checkStatus(Request $request)
    {
        $request->validate(['scene_id' => 'required|exists:scenes,id']);
        $scene = Scene::findOrFail($request->scene_id);

        if ($scene->audio_path) {
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'audio_url' => $scene->audio_url,
                'audio_path' => $scene->audio_path
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'pending'
        ]);
    }

    /**
     * Bulk generate voices for all scenes in a chapter (Queued).
     */
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $chapter = Chapter::with(['video', 'scenes'])->findOrFail($request->chapter_id);
        
        if ($chapter->video->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $costPerScene = $user->plan->voice_token_cost ?? 50;
        $totalScenes = count($chapter->scenes);
        $totalCost = $totalScenes * $costPerScene;

        if (!$user->isAdmin() && !$user->hasCredits($totalCost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$totalCost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        // Deduct Tokens
        if (!$user->isAdmin()) {
            $user->deductCredits($totalCost, 'voice');
        }

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        foreach ($chapter->scenes as $scene) {
            \App\Jobs\GenerateVoiceOverJob::dispatch(
                $scene->id,
                $request->voice_id,
                $options,
                $user->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Dispatched synthesis for {$totalScenes} scenes",
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }
}
