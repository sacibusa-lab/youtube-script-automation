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
     * Generate or regenerate voice for a specific scene with fine-tune parameters.
     */
    public function generate(Request $request, VoiceOverService $voiceService)
    {
        $request->validate([
            'scene_id' => 'required|exists:scenes,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $scene = Scene::with('chapter.video')->findOrFail($request->scene_id);
        
        // Authorization Check
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

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        $audioPath = $voiceService->generate($scene, $request->voice_id, $options);

        if ($audioPath) {
            // Deduct Tokens
            if (!$user->isAdmin()) {
                $user->deductCredits($cost, 'voice');
            }

            return response()->json([
                'success' => true,
                'audio_url' => asset('storage/' . $audioPath),
                'audio_path' => $audioPath,
                'scene_id' => $scene->id,
                'voice_id' => $request->voice_id,
                'tokens_remaining' => $user->fresh()->voiceTokensBalance()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Local bridge execution failed'], 500);
    }
    /**
     * Bulk generate voices for all scenes in a chapter.
     */
    public function bulkGenerate(Request $request, VoiceOverService $voiceService)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $chapter = Chapter::with(['video', 'scenes'])->findOrFail($request->chapter_id);
        
        // Authorization Check
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
                'message' => "Insufficient voice tokens for bulk generation. Required: {$totalCost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        $results = [];
        $total = count($chapter->scenes);
        $successCount = 0;

        foreach ($chapter->scenes as $scene) {
            $audioPath = $voiceService->generate($scene, $request->voice_id, $options);
            if ($audioPath) {
                $successCount++;
                $results[] = [
                    'scene_id' => $scene->id,
                    'audio_url' => asset('storage/' . $audioPath),
                    'audio_path' => $audioPath
                ];
            }
        }

        if ($successCount > 0 && !$user->isAdmin()) {
            $user->deductCredits($successCount * $costPerScene, 'voice');
        }

        return response()->json([
            'success' => $successCount > 0,
            'processed' => $total,
            'succeeded' => $successCount,
            'results' => $results,
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }
}
