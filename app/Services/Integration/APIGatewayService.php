<?php

namespace App\Services\Integration;

use App\Models\UserApiKey;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class APIGatewayService
{
    /**
     * Store or update a user's API key.
     *
     * @param int $userId
     * @param string $provider
     * @param string $apiKey
     * @return UserApiKey
     */
    public function storeApiKey(?int $userId, string $provider, string $apiKey, ?string $label = null): UserApiKey
    {
        return UserApiKey::create([
            'user_id' => $userId,
            'provider' => $provider,
            'api_key' => Crypt::encryptString($apiKey),
            'label' => $label ?? (ucfirst($provider) . ' Key'),
            'is_active' => true,
            'priority' => UserApiKey::where('user_id', $userId)->where('provider', $provider)->count()
        ]);
    }

    /**
     * Retrieve a decrypted API key for a user and provider.
     *
     * @param int $userId
     * @param string $provider
     * @return string|null
     */
    public function getActiveKey(?int $userId, string $provider): ?string
    {
        // 1. Try to find an active PRIMARY system key
        $record = UserApiKey::whereNull('user_id')
            ->where('provider', $provider)
            ->where('is_active', true)
            ->where('is_primary', true)
            ->first();

        // 2. Fallback to first active system key if no primary is set
        if (!$record) {
            $record = UserApiKey::whereNull('user_id')
                ->where('provider', $provider)
                ->where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();
        }

        if (!$record) {
            return null;
        }

        try {
            return Crypt::decryptString($record->api_key);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt API key for user {$userId} provider {$provider}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all active decrypted keys for failover support.
     *
     * @param int $userId
     * @param string $provider
     * @return array
     */
    public function getAvailableKeys(?int $userId, string $provider): array
    {
        // Strictly use system keys for failover support
        return UserApiKey::whereNull('user_id')
            ->where('provider', $provider)
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('priority', 'asc')
            ->get()
            ->map(function ($record) {
                try {
                    return [
                        'id' => $record->id,
                        'key' => Crypt::decryptString($record->api_key)
                    ];
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Test if an API key is valid by making a minimal request to the provider.
     *
     * @param string $provider
     * @param string $apiKey
     * @return bool
     */
    public function testConnection(string $provider, string $apiKey): bool
    {
        // Simple strategy pattern could be used here for different providers
        try {
            switch ($provider) {
                case 'openrouter':
                    // Verify key using OpenRouter's auth endpoint
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                        'Authorization' => "Bearer {$apiKey}"
                    ])->get('https://openrouter.ai/api/v1/auth/key');
                    return $response->successful();

                case 'together':
                    // Verify key via Together AI models endpoint
                    $response = \Illuminate\Support\Facades\Http::timeout(10)->withToken($apiKey)->get('https://api.together.xyz/v1/models');
                    return $response->successful();

                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::warning("API Connection test failed for {$provider}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the globally selected primary provider.
     */
    public function getPrimaryProvider(): string
    {
        $record = UserApiKey::whereNull('user_id')
            ->where('is_active', true)
            ->where('is_primary', true)
            ->first();

        return $record ? $record->provider : 'openrouter'; // Fallback to openrouter
    }
}
