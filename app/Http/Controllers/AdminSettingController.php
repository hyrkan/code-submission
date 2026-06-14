<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AiService;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        $providers = AiService::getSupportedProviders();
        $currentProvider = Setting::get('ai_provider', config('ai.default_provider', 'mimo'));

        $settings = [];
        foreach ($providers as $key => $provider) {
            $settings[$key] = [
                'name' => $provider['name'],
                'description' => $provider['description'],
                'api_key' => Setting::get("ai_{$key}_api_key", ''),
                'base_url' => Setting::get("ai_{$key}_base_url", $provider['base_url']),
                'model' => Setting::get("ai_{$key}_model", $provider['model']),
            ];
        }

        return view('admin.settings.index', compact('providers', 'currentProvider', 'settings'));
    }

    /**
     * Save AI settings.
     */
    public function saveAi(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|string|in:mimo,gemini,claude,openai',
        ]);

        $providers = AiService::getSupportedProviders();

        // Save the active provider
        Setting::set('ai_provider', $request->ai_provider, 'ai', 'string');

        // Save each provider's settings
        foreach (array_keys($providers) as $key) {
            if ($request->filled("{$key}_api_key")) {
                Setting::set("ai_{$key}_api_key", $request->input("{$key}_api_key"), 'ai', 'string');
            }
            if ($request->filled("{$key}_base_url")) {
                Setting::set("ai_{$key}_base_url", $request->input("{$key}_base_url"), 'ai', 'string');
            }
            if ($request->filled("{$key}_model")) {
                Setting::set("ai_{$key}_model", $request->input("{$key}_model"), 'ai', 'string');
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'AI settings saved successfully.');
    }

    /**
     * Test the AI connection.
     */
    public function testAi(Request $request)
    {
        // Accept provider from query params (GET) or body (POST)
        $provider = $request->input('provider', Setting::get('ai_provider', config('ai.default_provider', 'mimo')));

        try {
            $ai = new AiService($provider, [
                'api_key' => $request->input('api_key'),
                'base_url' => $request->input('base_url'),
                'model' => $request->input('model'),
            ]);
            $result = $ai->testConnection();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check credit/token balance.
     */
    public function checkBalance(Request $request)
    {
        $provider = $request->input('provider');
        $apiKey = $request->input('api_key');

        try {
            $ai = new AiService($provider, [
                'api_key' => $apiKey,
            ]);
            $details = $ai->getKeyDetails();

            if ($details && isset($details['data'])) {
                return response()->json([
                    'success' => true,
                    'data' => $details['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve key details.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}