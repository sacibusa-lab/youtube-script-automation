<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Chapter;
use App\Models\Scene;
use App\Services\Media\VoiceOverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryVoiceController extends Controller
{
    /**
     * Display the dedicated Story Voice Studio.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Video::query();
            
        // Show projects that are ready for voice generation (same as Studio)
        $query->whereIn('status', ['approved', 'completed', 'assembling', 'assembly_failed']);
        
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $projects = $query->with(['chapters.scenes', 'scenes', 'generatedTitles' => function($q) {
                $q->where('is_selected', true);
            }])
            ->latest()
            ->limit(100)
            ->get();

        return view('story-voices.index', compact('projects'));
    }

    /**
     * Generate or regenerate voice for a specific scene (Queued).
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        $cost = $user->plan?->voice_token_cost ?? 50;

        $request->validate([
            'scene_id' => 'required|exists:scenes,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $scene = Scene::with('chapter.video')->findOrFail($request->scene_id);
        
        if ($scene->chapter->video->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $cost = max(1, mb_strlen($scene->content ?? ''));

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        // Deduct Tokens Upfront
        $user->deductCredits($cost, 'voice');

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        // Dispatch Job
        \App\Jobs\GenerateVoiceOverJob::dispatch(
            $scene->id,
            $request->voice_id,
            $options,
            $user->id,
            \App\Models\Scene::class
        );

        return response()->json([
            'success' => true,
            'message' => 'Voice generation started in background',
            'scene_id' => $scene->id,
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Generate or regenerate voice for a Megahook (Queued).
     */
    public function generateMegahook(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:generated_titles,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $title = \App\Models\GeneratedTitle::with('video')->findOrFail($request->title_id);
        
        if ($title->video->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $cost = max(1, mb_strlen($title->title ?? ''));

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        // Deduct Tokens
        $user->deductCredits($cost, 'voice');

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        // Dispatch Job for GeneratedTitle
        \App\Jobs\GenerateVoiceOverJob::dispatch(
            $title->id,
            $request->voice_id,
            $options,
            $user->id,
            \App\Models\GeneratedTitle::class
        );

        return response()->json([
            'success' => true,
            'message' => 'Megahook synthesis started in background',
            'title_id' => $title->id,
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Check the status of a voice generation.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'scene_id' => 'nullable|exists:scenes,id',
            'title_id' => 'nullable|exists:generated_titles,id',
        ]);

        if ($request->scene_id) {
            $scene = Scene::findOrFail($request->scene_id);
            if ($scene->audio_path) {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'audio_url' => $scene->audio_url,
                    'audio_path' => $scene->audio_path
                ]);
            }
        } elseif ($request->title_id) {
            $title = \App\Models\GeneratedTitle::findOrFail($request->title_id);
            if ($title->mega_hook_audio_path) {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'audio_url' => $title->mega_hook_audio_url,
                    'audio_path' => $title->mega_hook_audio_path
                ]);
            }
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
        
        if ($chapter->video->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $totalChars = $chapter->scenes->sum(fn($s) => mb_strlen($s->content));
        $totalCost = max(1, $totalChars);

        if (!$user->isAdmin() && !$user->hasCredits($totalCost, 'voice')) {
            return response()->json(['success' => false, 'message' => "Insufficient tokens for bulk generation ({$totalCost} tokens required). Balance: " . $user->voiceTokensBalance()], 402);
        }

        // Deduct Tokens
        $user->deductCredits($totalCost, 'voice');

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        foreach ($chapter->scenes as $scene) {
            \App\Jobs\GenerateVoiceOverJob::dispatch(
                $scene->id,
                $request->voice_id,
                $options,
                $user->id,
                \App\Models\Scene::class
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Dispatched synthesis for " . $chapter->scenes->count() . " scenes",
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }
}
