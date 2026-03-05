<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserApiKey;
use App\Models\AIProductionRole;
use App\Services\Integration\APIGatewayService;
use App\Services\AI\AIManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ApiGatewayController extends Controller
{
    protected APIGatewayService $gateway;
    protected AIManager $aiManager;

    public function __construct(APIGatewayService $gateway, AIManager $aiManager)
    {
        $this->gateway = $gateway;
        $this->aiManager = $aiManager;
    }

    /**
     * Show the Admin API Gateway settings page.
     */
    public function index()
    {
        // System keys are where user_id is NULL
        $apiKeys = UserApiKey::whereNull('user_id')
            ->orderBy('provider')
            ->orderBy('priority')
            ->get()
            ->map(function ($key) {
                try {
                    $key->decrypted_value = Crypt::decryptString($key->api_key);
                } catch (\Exception $e) {
                    $key->decrypted_value = '';
                }
                return $key;
            });

        $groupedKeys = $apiKeys->groupBy('provider');

        $allActiveKeys = $apiKeys->where('is_active', true);

        $providers = [
            'openrouter' => 'OpenRouter',
            'together' => 'Together AI'
        ];

        // Fetch Dynamic Models from Primary Provider (OpenRouter or Together AI)
        $primaryProviderName = $this->gateway->getPrimaryProvider();
        $provider = $this->aiManager->getProvider($primaryProviderName);
        $availableModels = [];
        
        if ($provider && method_exists($provider, 'fetchModels')) {
            $fetchKey = $allActiveKeys->where('provider', $primaryProviderName)->where('is_primary', true)->first() 
                      ?? $allActiveKeys->where('provider', $primaryProviderName)->first();

            $apiKeyString = null;
            if ($fetchKey) {
                try {
                    $apiKeyString = Crypt::decryptString($fetchKey->api_key);
                } catch (\Exception $e) {}
            }
            $availableModels = $provider->fetchModels($apiKeyString);
        }

        // Fallback if fetch fails or no key
        if (empty($availableModels)) {
            $availableModels = [
                'openai/gpt-4o' => 'GPT-4o (Smart)',
                'openai/gpt-4o-mini' => 'GPT-4o Mini (Fast)',
                'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet (Nuanced)',
                'google/gemini-pro-1.5' => 'Gemini 1.5 Pro (Logical)',
                'deepseek/deepseek-chat' => 'DeepSeek V3 (Economic)',
                'mistralai/mistral-large' => 'Mistral Large (Reliable)'
            ];
        }

        return view('admin.api-gateway', [
            'apiKeys' => $groupedKeys,
            'allActiveKeys' => $allActiveKeys,
            'providers' => $providers,
            'roles' => AIProductionRole::all(),
            'availableModels' => $availableModels
        ]);
    }

    /**
     * Update the global strategy (primary key).
     */
    public function updateStrategy(Request $request)
    {
        $validated = $request->validate([
            'primary_api_key_id' => 'nullable|exists:user_api_keys,id',
        ]);

        // Clear ALL global primary keys first
        UserApiKey::whereNull('user_id')->update(['is_primary' => false]);

        if ($validated['primary_api_key_id']) {
            $apiKey = UserApiKey::findOrFail($validated['primary_api_key_id']);
            if ($apiKey->user_id !== null) abort(403);
            
            $apiKey->update(['is_primary' => true]);
        }

        return back()->with('success', 'Global system strategy updated.');
    }

    /**
     * Store or update a system-level API key.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'api_key' => 'required|string|min:10',
            'label' => 'nullable|string|max:50',
        ]);

        // Verify connection before saving
        if (!$this->gateway->testConnection($validated['provider'], $validated['api_key'])) {
            return back()->with('error', "Failed to connect to {$validated['provider']} with the provided key.");
        }

        // Store as system key (user_id = null)
        $this->gateway->storeApiKey(null, $validated['provider'], $validated['api_key'], $validated['label']);

        return back()->with('success', "{$validated['provider']} key added to System Defaults.");
    }

    /**
     * Toggle the status of a system-level API key.
     */
    public function toggleStatus(UserApiKey $apiKey)
    {
        if ($apiKey->user_id !== null) abort(403, 'This is not a system key.');
        
        $apiKey->update(['is_active' => !$apiKey->is_active]);
        
        return back()->with('success', "System key status updated.");
    }

    /**
     * Delete a system-level API key.
     */
    public function destroy(UserApiKey $apiKey)
    {
        if ($apiKey->user_id !== null) abort(403, 'This is not a system key.');
        
        $apiKey->delete();
        
        return back()->with('success', "System API Key deleted.");
    }

    /**
     * Test a system-level API key.
     */
    public function test(UserApiKey $apiKey)
    {
        if ($apiKey->user_id !== null) abort(403, 'This is not a system key.');

        try {
            $decrypted = Crypt::decryptString($apiKey->api_key);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to decrypt key.');
        }

        if ($this->gateway->testConnection($apiKey->provider, $decrypted)) {
            return back()->with('success', "System connection to {$apiKey->provider} is healthy.");
        }

        return back()->with('error', "Connection test failed for system {$apiKey->provider} key.");
    }
    /**
     * Set a system key as primary for its provider.
     */
    public function setPrimary(UserApiKey $apiKey)
    {
        if ($apiKey->user_id !== null) abort(403, 'This is not a system key.');

        // Clear existing primary for this provider
        UserApiKey::whereNull('user_id')
            ->where('provider', $apiKey->provider)
            ->update(['is_primary' => false]);

        // Set this one as primary
        $apiKey->update(['is_primary' => true]);

        return back()->with('success', "Primary key for {$apiKey->provider} updated.");
    }

    /**
     * Update the assigned model for a specific production role.
     */
    public function updateRole(Request $request, AIProductionRole $role)
    {
        $validated = $request->validate([
            'selected_model' => 'required|string',
        ]);

        $role->update([
            'selected_model' => $validated['selected_model']
        ]);

        return back()->with('success', "Production Role [{$role->name}] updated to {$validated['selected_model']}.");
    }
}
