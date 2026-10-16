<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\Country;
use App\Models\Provider;
use App\Models\ProviderAccount;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProviderSettingsController extends Controller
{
    /**
     * Get PVADeals configuration and live connectivity status.
     */
    public function getStatus(): JsonResponse
    {
        $client = new PvaDealsClient();
        $hasKey = $client->hasApiKey();
        $apiKey = $client->getApiKey();
        $balance = null;
        $isConnected = false;
        $error = null;

        if ($hasKey) {
            try {
                $balance = $client->getBalance();
                $isConnected = $balance !== null;
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        $maskedKey = '';
        if (!empty($apiKey)) {
            $len = strlen($apiKey);
            $maskedKey = $len > 8 ? substr($apiKey, 0, 6) . '...' . substr($apiKey, -4) : '••••••••';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'provider' => 'pvadeals',
                'name' => 'PVADeals v3 API Gateway',
                'configured' => $hasKey,
                'connected' => $isConnected,
                'masked_key' => $maskedKey,
                'balance_usd' => $balance,
                'services_count' => Service::where('is_active', true)->count(),
                'countries_count' => Country::where('is_active', true)->count(),
                'error' => $error,
            ],
        ]);
    }

    /**
     * Save / Update PVADeals API key and test connection.
     */
    public function saveKey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'api_key' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $key = trim($validated['api_key']);

        // 1. Test key against live PVADeals endpoint
        $testClient = new PvaDealsClient($key);
        $balance = $testClient->getBalance();
        $rawBalance = $testClient->getRawBalance();

        if ($balance === null && !empty($rawBalance['status']) && $rawBalance['status'] === 401) {
            return response()->json([
                'success' => false,
                'message' => 'PVADeals authentication failed: Invalid API key. Please verify your key on app.pvadeals.com.',
            ], 422);
        }

        // 2. Save in database provider_accounts table
        $provider = Provider::firstOrCreate(
            ['code' => 'pvadeals'],
            ['name' => 'PVADeals v3 API Gateway', 'type' => 'aggregator', 'priority' => 1, 'status' => 'active']
        );
        $provider->update(['priority' => 1, 'status' => 'active']);

        ProviderAccount::updateOrCreate(
            ['provider_id' => $provider->id],
            [
                'name' => 'PVADeals Production Account',
                'credentials' => [
                    'api_key' => $key,
                    'base_url' => config('services.pvadeals.base_url', 'https://prod-v3.pvadeals.com/v3/api'),
                ],
                'status' => 'active',
                'currency_code' => 'USD',
                'balance' => $balance ?? 0.00,
                'last_synced_at' => now(),
            ]
        );

        // 3. Update .env file
        $this->updateEnvFile('PVADEALS_API_KEY', $key);

        // 4. Also demote Twilio priority
        Provider::where('code', 'twilio')->update(['priority' => 10]);

        try {
            Artisan::call('config:clear');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'PVADeals API key successfully saved and verified!',
            'data' => [
                'balance_usd' => $balance,
                'masked_key' => substr($key, 0, 6) . '...' . substr($key, -4),
            ],
        ]);
    }

    /**
     * Pull and sync live services from PVADeals catalog.
     */
    public function syncCatalog(): JsonResponse
    {
        $client = new PvaDealsClient();
        if (!$client->hasApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot sync catalog: PVADeals API key is not configured.',
            ], 422);
        }

        $pvaServices = $client->getAllServices(true);
        if (empty($pvaServices)) {
            return response()->json([
                'success' => false,
                'message' => 'No services returned by PVADeals API.',
            ], 502);
        }

        $syncedCount = 0;
        $catUniversal = ServiceCategory::firstOrCreate(['name' => 'Universal / Other'], ['description' => 'Universal verifications']);

        foreach ($pvaServices as $s) {
            $name = trim($s['name'] ?? '');
            if (empty($name)) continue;

            $code = Str::slug($name);
            $service = Service::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => $s['description'] ?? "PVADeals verification service ({$name})",
                    'is_active' => true,
                ]
            );
            $catUniversal->services()->syncWithoutDetaching([$service->id]);
            $syncedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully synced {$syncedCount} services from PVADeals!",
            'data' => [
                'synced_count' => $syncedCount,
                'total_services' => Service::where('is_active', true)->count(),
            ],
        ]);
    }

    /**
     * Safely update a key-value in .env file.
     */
    protected function updateEnvFile(string $key, string $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);
        if (preg_match("/^{$key}=.*/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }

        file_put_contents($path, $content);
    }
}
