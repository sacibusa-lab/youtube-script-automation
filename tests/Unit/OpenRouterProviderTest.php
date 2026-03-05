<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AI\OpenRouterProvider;
use App\Services\AI\Exceptions\ProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class OpenRouterProviderTest extends TestCase
{
    public function test_generate_retry_logic_on_rate_limit()
    {
        Config::set('services.openrouter.api_key', 'test_key');
        Config::set('ai.providers.openrouter.model', 'gpt-4o-mini');
        Config::set('ai.providers.openrouter.retry_attempts', 1);
        Config::set('ai.providers.openrouter.retry_backoff', [0]); // No delay for testing

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
                ->push(['error' => 'Rate limited'], 429)
                ->push([
                    'choices' => [['message' => ['content' => 'Success body']]],
                    'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 20],
                    'model' => 'gpt-4o-mini'
                ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $response = $provider->generate('Hello');

        $this->assertEquals('Success body', $response->content);
        $this->assertEquals(10, $response->inputTokens);
        Http::assertSentCount(2);
    }

    public function test_generate_model_fallback_on_error()
    {
        Config::set('services.openrouter.api_key', 'test_key');
        Config::set('ai.providers.openrouter.model', 'model-1');
        Config::set('ai.providers.openrouter.fallback_models', ['model-2']);
        Config::set('ai.providers.openrouter.retry_attempts', 0);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
                ->push(['error' => 'Critical error'], 500) // model-1 fails
                ->push([
                    'choices' => [['message' => ['content' => 'Fallback success']]],
                    'usage' => ['prompt_tokens' => 5, 'completion_tokens' => 5],
                    'model' => 'model-2'
                ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $response = $provider->generate('Hello');

        $this->assertEquals('Fallback success', $response->content);
        $this->assertEquals('model-2', $response->model);
        Http::assertSentCount(2);
    }

    public function test_generate_throws_exception_when_all_fail()
    {
        Config::set('services.openrouter.api_key', 'test_key');
        Config::set('ai.providers.openrouter.model', 'model-1');
        Config::set('ai.providers.openrouter.fallback_models', []);
        Config::set('ai.providers.openrouter.retry_attempts', 0);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(['error' => 'Fail'], 500)
        ]);

        $this->expectException(ProviderException::class);
        
        $provider = new OpenRouterProvider();
        $provider->generate('Hello');
    }
}
