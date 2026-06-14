<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $provider;
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected ?string $lastError = null;

    public function __construct(?string $provider = null, array $config = [])
    {
        $this->provider = $provider ?? $this->getSetting('ai_provider', config('ai.default_provider', 'mimo'));
        
        $this->apiKey = $config['api_key'] ?? $config['token'] ?? $config['api_token'] ?? $this->getSetting("ai_{$this->provider}_api_key", '');
        
        $customBaseUrl = $config['base_url'] ?? $config['endpoint'] ?? $this->getSetting("ai_{$this->provider}_base_url");
        if ($customBaseUrl) {
            $this->baseUrl = rtrim($customBaseUrl, '/');
        } else {
            $this->baseUrl = $this->resolveBaseUrl();
        }
        
        $this->model = $config['model'] ?? $this->getSetting("ai_{$this->provider}_model", config("ai.providers.{$this->provider}.model", ''));
    }

    /**
     * Get setting directly from DB, bypassing Setting model to avoid OPcache issues.
     */
    protected function getSetting(string $key, $default = null)
    {
        $value = DB::table('settings')->where('key', $key)->value('value');
        return $value !== null ? $value : $default;
    }

    protected function resolveBaseUrl(): string
    {
        $dbUrl = $this->getSetting("ai_{$this->provider}_base_url");
        if ($dbUrl) {
            return rtrim($dbUrl, '/');
        }
        return rtrim(config("ai.providers.{$this->provider}.base_url", 'https://api.openai.com/v1'), '/');
    }

    public function chat(string $systemPrompt, string $userMessage, array $options = []): ?string
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException("AI API key not configured for provider: {$this->provider}");
        }

        $this->lastError = null;
        $maxTokens = $options['max_tokens'] ?? 4096;
        $temperature = $options['temperature'] ?? 0.7;

        try {
            // Use Gemini native format for Gemini provider ONLY if it uses the Google base URL
            if ($this->provider === 'gemini' && str_contains($this->baseUrl, 'googleapis.com')) {
                return $this->sendGeminiRequest($systemPrompt, $userMessage, $maxTokens, $temperature);
            }

            // Use OpenAI-compatible format for all other providers/endpoints
            return $this->sendOpenAiRequest($systemPrompt, $userMessage, $maxTokens, $temperature);
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Log::error('AI API Exception', [
                'provider' => $this->provider,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send request using OpenAI-compatible API format.
     */
    protected function sendOpenAiRequest(string $systemPrompt, string $userMessage, int $maxTokens, float $temperature): ?string
    {
        $url = "{$this->baseUrl}/chat/completions";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(60)->post($url, [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        if ($response->successful()) {
            $content = data_get($response->json(), 'choices.0.message.content');
            return $content ?: null;
        }

        $errorBody = $response->body();
        $this->lastError = "HTTP {$response->status()}: " . mb_substr($errorBody, 0, 500);

        Log::error('AI API Error (OpenAI)', [
            'provider' => $this->provider,
            'url' => $url,
            'model' => $this->model,
            'status' => $response->status(),
            'body' => $errorBody,
        ]);

        return null;
    }

    /**
     * Send request using Google Gemini native API format.
     */
    protected function sendGeminiRequest(string $systemPrompt, string $userMessage, int $maxTokens, float $temperature): ?string
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($url, [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                ['parts' => [['text' => $userMessage]]],
            ],
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature' => $temperature,
            ],
        ]);

        if ($response->successful()) {
            $content = data_get($response->json(), 'candidates.0.content.parts.0.text');
            return $content ?: null;
        }

        $errorBody = $response->body();
        $this->lastError = "HTTP {$response->status()}: " . mb_substr($errorBody, 0, 500);

        Log::error('AI API Error (Gemini)', [
            'url' => $url,
            'model' => $this->model,
            'status' => $response->status(),
            'body' => $errorBody,
        ]);

        return null;
    }

    public function analyzeCode(string $code, string $language, string $challengeDescription, ?string $expectedOutput = null, ?string $gradingCriteria = null, int $maxPoints = 10): ?string
    {
        $systemPrompt = <<<PROMPT
You are an expert programming instructor and code reviewer. Analyze the student's code submission and provide detailed feedback.

Your response should include:
1. **Correctness** - Does the code solve the problem correctly?
2. **Code Quality** - Is the code well-structured, readable, and following best practices?
3. **Efficiency** - Are there any performance concerns?
4. **Suggestions** - Specific improvements the student can make.
5. **Score Recommendation** - A score out of the total points based on the grading criteria.

Be constructive, educational, and encouraging in your feedback.

CRITICAL INSTRUCTION: You MUST end your response with a tag indicating the integer score you recommend for this submission out of {$maxPoints} points, formatted exactly like:
[SCORE]number[/SCORE]
For example, if the student gets 8 points, end the response with:
[SCORE]8[/SCORE]
PROMPT;

        $userMessage = "**Programming Language:** {$language}\n\n";
        $userMessage .= "**Challenge Description:**\n{$challengeDescription}\n\n";

        if ($expectedOutput) {
            $userMessage .= "**Expected Output:**\n{$expectedOutput}\n\n";
        }

        if ($gradingCriteria) {
            $userMessage .= "**Grading Criteria:**\n{$gradingCriteria}\n\n";
        }

        $userMessage .= "**Student's Code:**\n```\n{$code}\n```";

        return $this->chat($systemPrompt, $userMessage, ['temperature' => 0.3]);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl) && !empty($this->model);
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public static function getSupportedProviders(): array
    {
        return config('ai.providers', []);
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'API key not configured.'];
        }

        $this->lastError = null;
        $result = $this->chat('You are a helpful assistant.', 'Say "Connection successful" in one sentence.', ['max_tokens' => 50]);

        if ($result) {
            return ['success' => true, 'message' => 'Connection successful!', 'response' => $result];
        }

        $errorDetail = $this->lastError ? " Error: {$this->lastError}" : '';
        return ['success' => false, 'message' => "No response from AI provider.{$errorDetail}"];
    }

    /**
     * Get OpenRouter API key details (credits/limit/usage).
     */
    public function getKeyDetails(): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            if ($this->provider === 'mimo' || str_contains($this->baseUrl, 'openrouter.ai')) {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])->timeout(10)->get('https://openrouter.ai/api/v1/auth/key');

                if ($response->successful()) {
                    return $response->json();
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch key details', [
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}