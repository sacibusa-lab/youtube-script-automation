<?php

return [
    'default_provider' => 'openrouter',
    'fallback_enabled' => false,
    
    'providers' => [
        'openrouter' => [
            'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
            'fallback_models' => [
                'anthropic/claude-3-5-sonnet',
                'deepseek/deepseek-chat',
                'google/gemini-pro-1.5',
            ],
            'retry_attempts' => 3,
            'retry_backoff' => [1000, 2000, 4000],
            'input_cost_per_1k' => 0.00015,  // gpt-4o-mini pricing
            'output_cost_per_1k' => 0.0006,
        ],
    ],
];
