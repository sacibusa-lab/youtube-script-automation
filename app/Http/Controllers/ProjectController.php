<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Channel;
use App\Models\Niche;
use App\Models\ContentStructure;
use App\Models\EmotionalTone;
use App\Models\GeneratedTitle;
use App\Services\Export\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateConceptsJob;
use App\Jobs\GenerateVideoStructureJob;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        
        $videos = Video::with('chapters')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(12);

        // AI Usage for this user
        $usageStats = DB::table('ai_usages')
            ->where('user_id', $userId)
            ->select([
                DB::raw('SUM(input_tokens + output_tokens) as total_tokens'),
                DB::raw('SUM(estimated_cost) as total_cost')
            ])
            ->first();

        return view('videos.index', [
            'videos' => $videos,
            'totalTokens' => $usageStats->total_tokens ?? 0,
            'totalCost' => $usageStats->total_cost ?? 0,
            'totalCompleted' => Video::where('user_id', $userId)->where('status', 'completed')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Fetch Niches grouped by Tier
        $niches = Niche::all()->groupBy('tier');
        
        // 2. Fetch Channels (Select list)
        $channels = Channel::all();

        // 3. Fetch Antigravity Layers
        $structures = ContentStructure::all(); // Layer 2
        $emotions = EmotionalTone::all();     // Layer 3

        return view('videos.create', compact('niches', 'channels', 'structures', 'emotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate Input
        $validated = $request->validate([
             'niche_id' => 'required|exists:niches,id',
             'content_structure_id' => 'required|exists:content_structures,id', // Layer 2
             'emotional_tone_id' => 'required|exists:emotional_tones,id',       // Layer 3
             'channel_id' => 'nullable|exists:channels,id', // Make nullable if not selected
             'hybrid_intensity' => 'required|integer|min:0|max:100', // e.g., 30, 50, 70
             'risk_mode' => 'required|string', // e.g., "Safe", "Edgy"
             'tier1_country' => 'required|string', // e.g., "US", "UK"
             'duration_minutes' => 'required|integer|min:1|max:120',
        ]);

        // 2. Get Related Models
        $niche = Niche::findOrFail($validated['niche_id']);
        
        // Default Channel if not selected (Temporary)
        $channelId = $validated['channel_id'] ?? Channel::first()->id;

        // 3. Create Video Record (Concept Phase)
        // Store Antigravity specific configs in metadata
        $video = Video::create([
            'user_id' => Auth::id(),
            'channel_id' => $channelId,
            'niche_id' => $niche->id,
            'content_structure_id' => $validated['content_structure_id'],
            'emotional_tone_id' => $validated['emotional_tone_id'],
            'topic' => $niche->name, // Niche name as starting topic
            'niche' => $niche->name,
            'sub_niche' => null,
            'tier1_country' => $validated['tier1_country'],
            'duration_minutes' => $validated['duration_minutes'],
            'chapter_count' => 10,
            'status' => 'pending',
            'metadata' => [
                'hybrid_intensity' => $validated['hybrid_intensity'],
                'risk_mode' => $validated['risk_mode'],
            ]
        ]);

        // Dispatch background job to generate CONCEPTS first
        \App\Jobs\GenerateConceptsJob::dispatch($video);

        return redirect()->route('projects.show', $video)
            ->with('success', 'Project created! Generating 5 viral concepts for you to choose from...');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'nullable|string',
            'topic' => 'nullable|string',
            'niche' => 'nullable|string',
        ]);

        $project->update($validated);

        return back()->with('success', 'Project updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $project->load(['chapters.scenes', 'generatedTitles']); // Eager load relationships
        
        return view('videos.show', compact('project'));
    }

    /**
     * Export project assets.
     */
    public function export(Video $project, ExportService $exportService)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $zipUrl = $exportService->exportAssets($project->id);

        if ($zipUrl) {
            return redirect($zipUrl);
        }

        return back()->with('error', 'Failed to generate export package.');
    }
    
    /**
     * Retry the concept generation for a failed project.
     */
    public function retry(Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        // Clear existing generated titles to avoid stale data
        $project->generatedTitles()->delete();

        $project->update([
            'status' => 'pending',
            'title_variations' => null
        ]);

        // Dispatch background job to re-generate CONCEPTS
        \App\Jobs\GenerateConceptsJob::dispatch($project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Retrying generation! Our AI is working on 5 new concepts...');
    }

    /**
     * Select a specific concept (Title + Hook) for the project.
     */
    public function selectTitle(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title_id' => 'required|exists:generated_titles,id',
        ]);

        $generatedTitle = GeneratedTitle::findOrFail($validated['title_id']);

        $project->update([
            'selected_title' => $generatedTitle->title,
            'status' => 'generating_concept_details',
        ]);

        // Stage 2: Detailed Hook, Thumbnail Prompt, and Short Script
        \App\Jobs\GenerateTitleDetailsJob::dispatch($project, $generatedTitle->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Title selected! Architecting the narrative hook and visual concept...');
    }

    /**
     * Final step: Launch the full video generation mission.
     */
    public function launchMission(Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        // Finalize metadata from the selected title
        $generatedTitle = GeneratedTitle::where('video_id', $project->id)
            ->where('title', $project->selected_title)
            ->first();

        if (!$generatedTitle) {
            return back()->with('error', 'No concept found for this title.');
        }

        $project->update([
            'mega_hook' => $generatedTitle->mega_hook,
            'thumbnail_concept' => $generatedTitle->thumbnail_concept,
            'status' => 'architecting_chapters',
        ]);

        // Dispatch Video Structure Job
        \App\Jobs\GenerateVideoStructureJob::dispatch($project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Mission Launched! Architecting your cinematic video story bible and chapters...');
    }

    /**
     * Select a specific strategy for the project (CreatorFlow Logic).
     */
    public function selectStrategy(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'strategy_index' => 'required|integer',
        ]);

        $strategies = $project->strategies;
        if (!isset($strategies[$validated['strategy_index']])) {
            return back()->with('error', 'Invalid concept selection.');
        }

        $selectedStrategy = $strategies[$validated['strategy_index']];

        $project->update([
            'selected_title' => $selectedStrategy['title'],
            'mega_hook' => $selectedStrategy['megaHooks'][0] ?? null,
            'thumbnail_concept' => $selectedStrategy['thumbnailConcepts'][0]['prompt'] ?? $selectedStrategy['thumbnailConcepts'][0]['description'] ?? null,
            'thumbnail_visual_prompt_data' => $selectedStrategy['thumbnailConcepts'][0] ?? null,
            'platform_data' => $selectedStrategy['platform_adaptations'] ?? null,
            'status' => 'architecting_chapters',
        ]);

        // Dispatch Video Structure Job (One-Off Workflow)
        \App\Jobs\GenerateVideoStructureJob::dispatch($project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Concept selected! Architecting your cinematic video story bible and chapters...');
    }

    /**
     * Regenerate the mega-hook for a specific title variant.
     */
    public function regenerateHook(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title_id' => 'required|exists:generated_titles,id'
        ]);

        $title = GeneratedTitle::where('id', $validated['title_id'])->where('video_id', $project->id)->firstOrFail();

        $aiManager = app(\App\Services\AI\AIManager::class);
        $promptBuilder = app(\App\Services\AI\PromptBuilder::class);

        $prompt = $promptBuilder->buildMegaHookPrompt($title->title, $project->niche, $project->tier1_country);
        
        try {
            $response = $aiManager->generate($prompt, [], $project->user_id, 'hook', $project->id);
            $content = json_decode($response->content, true)['content'] ?? $response->content;
            
            $title->update(['mega_hook' => $content]);

            return response()->json([
                'success' => true,
                'mega_hook' => $content
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to regenerate hook: " . $e->getMessage());
            return response()->json(['error' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Regenerate the thumbnail prompt for a specific title variant.
     */
    public function regenerateThumbnail(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title_id' => 'required|exists:generated_titles,id'
        ]);

        $title = GeneratedTitle::where('id', $validated['title_id'])->where('video_id', $project->id)->firstOrFail();

        $aiManager = app(\App\Services\AI\AIManager::class);
        $promptBuilder = app(\App\Services\AI\PromptBuilder::class);

        $prompt = $promptBuilder->buildThumbnailPrompt($title->title, $title->mega_hook, $project->niche);
        
        try {
            $response = $aiManager->generate($prompt, [], $project->user_id, 'thumbnail', $project->id);
            $content = json_decode($response->content, true)['content'] ?? $response->content;
            
            $title->update(['thumbnail_concept' => $content]);

            return response()->json([
                'success' => true,
                'thumbnail_concept' => $content
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to regenerate thumbnail prompt: " . $e->getMessage());
            return response()->json(['error' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Trigger image generation from the thumbnail prompt for a specific title variant.
     */
    public function generateThumbnailImage(GeneratedTitle $title)
    {
        if ($title->video->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (empty($title->thumbnail_concept)) {
            return response()->json(['error' => 'No thumbnail concept exists to generate from.'], 400);
        }

        $title->update(['thumbnail_status' => 'generating']);

        // Directly dispatch the job
        \App\Jobs\GenerateThumbnailImageJob::dispatch($title);

        return response()->json([
            'success' => true,
            'message' => 'Image generation triggered.'
        ]);
    }
    /**
     * Manually trigger image generation for a scene
     */
    public function generateSceneImage(\App\Models\Video $project, \App\Models\Chapter $chapter, \App\Models\Scene $scene)
    {
        if ($project->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized access.');
        }

        if (empty($scene->visual_prompt)) {
            return back()->with('error', 'No visual prompt exists for this scene.');
        }

        // Dispatch the single image job with null provider to allow resolution
        \App\Jobs\GenerateSingleImageJob::dispatch($scene, null);

        return back()->with('status', 'Visual Generation Engine engaged for Node ' . $scene->scene_number);
    }


    /**
     * Check the status of a specific scene's image generation (AJAX/Polling).
     */
    public function checkSceneImageStatus(\App\Models\Video $project, \App\Models\Chapter $chapter, \App\Models\Scene $scene)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user()->fresh();

        return response()->json([
            'status' => 'success',
            'image_url' => $scene->image_url ? url($scene->image_url) : null,
            'user_tokens' => [
                'script_credits' => $user->scriptCreditsBalance(),
                'image_tokens'   => $user->imageTokensBalance()
            ]
        ]);
    }

    /**
     * Persist or remove a title from saved favorites.
     */
    public function toggleBookmark(GeneratedTitle $title)
    {
        // Basic security check: title belongs to a video owned by user
        if ($title->video->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $title->update([
            'is_saved' => !$title->is_saved
        ]);

        return response()->json([
            'success' => true,
            'is_saved' => $title->is_saved
        ]);
    }

    public function checkTitleStatus(GeneratedTitle $title)
    {
        // Ownership check
        if ($title->video->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user()->fresh();

        return response()->json([
            'success' => true,
            'title' => $title->title,
            'mega_hook' => $title->mega_hook,
            'thumbnail_concept' => $title->thumbnail_concept,
            'thumbnail_url' => $title->thumbnail_url,
            'thumbnail_status' => $title->thumbnail_status,
            'short_script' => $title->short_script,
            'user_tokens' => [
                'script_credits' => $user->scriptCreditsBalance(),
                'image_tokens'   => $user->imageTokensBalance()
            ]
        ]);
    }

    /**
     * Display a collection of saved concepts.
     */
    public function bookmarks()
    {
        $userId = Auth::id();
        
        $bookmarks = GeneratedTitle::where('is_saved', true)
            ->whereHas('video', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('video')
            ->latest()
            ->paginate(12);

        return view('videos.bookmarks', compact('bookmarks'));
    }

    /**
     * Launch a new project based on an existing concept.
     */
    public function cloneFromConcept(GeneratedTitle $title)
    {
        if ($title->video->user_id !== Auth::id()) {
            abort(403);
        }

        $sourceVideo = $title->video;

        $hasDetails = !empty($title->mega_hook);

        // Create a new video record with the same settings
        $newProject = Video::create([
            'user_id' => Auth::id(),
            'channel_id' => $sourceVideo->channel_id,
            'niche_id' => $sourceVideo->niche_id,
            'content_structure_id' => $sourceVideo->content_structure_id,
            'emotional_tone_id' => $sourceVideo->emotional_tone_id,
            'topic' => $sourceVideo->topic,
            'niche' => $sourceVideo->niche,
            'sub_niche' => $sourceVideo->sub_niche,
            'tier1_country' => $sourceVideo->tier1_country,
            'duration_minutes' => $sourceVideo->duration_minutes,
            'chapter_count' => $sourceVideo->chapter_count,
            'status' => $hasDetails ? 'waiting_for_launch' : 'generating_concept_details',
            'selected_title' => $title->title,
            'mega_hook' => $title->mega_hook,
            'thumbnail_concept' => $title->thumbnail_concept,
            'thumbnail_visual_prompt_data' => $title->visual_prompt_data,
            'metadata' => array_merge($sourceVideo->metadata ?? [], [
                'is_cloned' => true,
                'original_video_id' => $sourceVideo->id,
            ])
        ]);

        // Clone the title record for the new project
        $newTitle = $title->replicate();
        $newTitle->video_id = $newProject->id;
        $newTitle->is_saved = false;
        $newTitle->save();

        if (!$hasDetails) {
            \App\Jobs\GenerateTitleDetailsJob::dispatch($newProject, $newTitle->id);
        }

        return redirect()->route('projects.show', $newProject)
            ->with('success', $hasDetails ? 'Concept cloned! Mission ready for launch.' : 'Concept cloned! Architecting narrative details...');
    }

    /**
     * Trigger manual generation for a specific chapter.
     */
    public function architectChapter(\App\Models\Video $project, \App\Models\Chapter $chapter)
    {
        if ($project->user_id !== Auth::id() || $chapter->video_id !== $project->id) {
            abort(403);
        }

        // Load all chapters for prompt context
        $allChapters = $project->chapters->toArray();

        // Dispatch background job for chapter narration
        \App\Jobs\GenerateChapterNarrationJob::dispatch($project, $chapter, $allChapters);

        return back()->with('success', "Architecting Chapter {$chapter->chapter_number}...");
    }

    /**
     * Approve and lock a generated chapter.
     */
    public function approveChapter(\App\Models\Video $project, \App\Models\Chapter $chapter)
    {
        if ($project->user_id !== Auth::id() || $chapter->video_id !== $project->id) {
            abort(403);
        }

        $chapter->update(['status' => 'approved']);

        // Check if all chapters are approved to finalize the video
        $allApproved = $project->chapters()->where('status', '!=', 'approved')->count() === 0;
        
        if ($allApproved) {
            $project->update(['status' => 'completed']);
        }

        return back()->with('success', "Chapter {$chapter->chapter_number} approved and locked!");
    }

    /**
     * Show the cinematic studio interface.
     */
    public function studio(Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow production-ready projects in the studio
        if (!in_array($project->status, ['approved', 'completed'])) {
            return redirect()->route('projects.show', $project)
                ->with('warning', 'The Studio is still generating your video architecture. Please wait for chapters to be finalized.');
        }

        $project->load(['chapters.scenes', 'niche', 'channel']);

        return view('videos.studio', compact('project'));
    }

    /**
     * Bulk save the state of the studio workshop.
     */
    public function saveStudioState(Request $request, Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.scenes' => 'required|array',
            'chapters.*.scenes.*.id' => 'required|exists:scenes,id',
            'chapters.*.scenes.*.narration_text' => 'nullable|string',
            'chapters.*.scenes.*.visual_prompt' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['chapters'] as $chapterData) {
                foreach ($chapterData['scenes'] as $sceneData) {
                    \App\Models\Scene::where('id', $sceneData['id'])
                        ->where('video_id', $project->id) // Security check
                        ->update([
                            'narration_text' => $sceneData['narration_text'] ?? '',
                            'visual_prompt' => $sceneData['visual_prompt'] ?? '',
                        ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the studio landing page (index of projects for studio).
     */
    public function studioIndex()
    {
        $userId = Auth::id();
        $videos = Video::with('chapters')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'completed']) // Only show project in active production
            ->latest()
            ->paginate(12);

        return view('videos.studio-index', compact('videos'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
