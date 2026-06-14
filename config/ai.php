<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supported providers and their default settings.
    | API keys are stored in the database via Settings > Config.
    | These are the default base URLs and models for each provider.
    |
    */

    'providers' => [

        'mimo' => [
            'name' => 'Xiaomi MiMo Code (via OpenRouter)',
            'base_url' => env('AI_MIMO_BASE_URL', 'https://openrouter.ai/api/v1'),
            'model' => env('AI_MIMO_MODEL', 'xiaomi/mimo-v2.5'),
            'description' => 'Xiaomi MiMo Code AI via OpenRouter',
        ],

        'gemini' => [
            'name' => 'Google Gemini Flash',
            'base_url' => env('AI_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'model' => env('AI_GEMINI_MODEL', 'gemini-2.0-flash'),
            'description' => 'Google Gemini Flash - Fast and efficient AI model',
        ],

        'claude' => [
            'name' => 'Anthropic Claude',
            'base_url' => env('AI_CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'),
            'model' => env('AI_CLAUDE_MODEL', 'claude-sonnet-4-20250514'),
            'description' => 'Anthropic Claude - Advanced reasoning and code analysis',
        ],

        'openai' => [
            'name' => 'OpenAI',
            'base_url' => env('AI_OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('AI_OPENAI_MODEL', 'gpt-4o'),
            'description' => 'OpenAI GPT - General purpose AI model',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    */
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'mimo'),

];