<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AI\AIRoleService;
use Illuminate\Support\Facades\Auth;

class AIRoleController extends Controller
{
    protected AIRoleService $roleService;

    public function __construct(AIRoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Execute a production role task via API.
     */
    public function execute(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:ai_production_roles,slug',
            'prompt' => 'required|string|min:5',
            'context' => 'nullable|array',
            'video_id' => 'nullable|exists:videos,id',
        ]);

        try {
            $context = $validated['context'] ?? [];
            $context['user_id'] = Auth::id();
            $context['video_id'] = $validated['video_id'] ?? null;

            $response = $this->roleService->executeRoleTask(
                $validated['role'],
                $validated['prompt'],
                $context
            );

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
