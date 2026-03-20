<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Chapter;
use App\Models\Scene;
use App\Services\Media\VoiceOverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VoiceGenerationController extends Controller
{
    /**
     * Display the independent Voice Generation Studio.
     */
    public function index()
    {
        return view('voice-generation.index');
    }

    /**
     * Generate voice for raw text (Independent Mode).
     */
    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'voice_id' => 'nullable|string',
            'speed' => 'nullable|numeric|between:0.5,2.0',
            'volume' => 'nullable|numeric|between:0.0,2.0',
        ]);

        $user = Auth::user();
        // 1 token per 1 character (mb_strlen for utf8 safety)
        $charCount = mb_strlen($request->text);
        $cost = max(1, $charCount); // Minimum 1 token

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        // Deduct Tokens
        $user->deductCredits($cost, 'voice');

        $service = new VoiceOverService();
        $options = [
            'speed' => (float) $request->input('speed', 1.0),
            'volume' => (float) $request->input('volume', 1.0),
        ];

        $path = $service->generateFromText(
            $request->text,
            $request->voice_id,
            $options
        );

        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Synthesis failed'], 500);
        }

        return response()->json([
            'success' => true,
            'audio_url' => Storage::disk('public')->url($path),
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Generate megahook for a specific title variant.
     */
    public function generateMegahook(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:generated_titles,id',
            'voice_id' => 'nullable|string',
        ]);

        $title = \App\Models\GeneratedTitle::findOrFail($request->title_id);
        $user = Auth::user();
        $cost = mb_strlen($title->title);
        $cost = max(1, $cost);

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        $user->deductCredits($cost, 'voice');

        \App\Jobs\GenerateVoiceOverJob::dispatch(
            $request->title_id,
            $request->voice_id,
            ['speed' => 1.0, 'volume' => 1.0],
            $user->id,
            \App\Models\GeneratedTitle::class
        );

        return response()->json([
            'success' => true,
            'message' => 'Megahook synthesis started',
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Bulk generate voices for a chapter.
     */
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'voice_id' => 'nullable|string',
        ]);

        $chapter = \App\Models\Chapter::with('scenes')->findOrFail($request->chapter_id);
        $totalChars = $chapter->scenes->sum(fn($s) => mb_strlen($s->content));
        $user = Auth::user();
        $cost = max(1, $totalChars);

        if (!$user->isAdmin() && !$user->hasCredits($cost, 'voice')) {
            return response()->json([
                'success' => false, 
                'message' => "Insufficient voice tokens. Required: {$cost}, Balance: " . $user->voiceTokensBalance()
            ], 402);
        }

        $user->deductCredits($cost, 'voice');

        foreach ($chapter->scenes as $scene) {
            \App\Jobs\GenerateVoiceOverJob::dispatch(
                $scene->id,
                $request->voice_id,
                ['speed' => 1.0, 'volume' => 1.0],
                $user->id,
                \App\Models\Scene::class
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk synthesis started for " . $chapter->scenes->count() . " scenes",
            'tokens_remaining' => $user->fresh()->voiceTokensBalance()
        ]);
    }

    /**
     * Check status of a synthesis job.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'title_id' => 'nullable|exists:generated_titles,id',
            'scene_id' => 'nullable|exists:scenes,id',
        ]);

        if ($request->title_id) {
            $model = \App\Models\GeneratedTitle::find($request->title_id);
            $path = $model->mega_hook_audio_path;
        } else {
            $model = \App\Models\Scene::find($request->scene_id);
            $path = $model->audio_path;
        }

        if ($path && Storage::disk('public')->exists($path)) {
            return response()->json([
                'status' => 'completed',
                'audio_url' => Storage::disk('public')->url($path),
                'tokens_remaining' => Auth::user()->voiceTokensBalance()
            ]);
        }

        return response()->json(['status' => 'pending']);
    }
}
