<?php

namespace App\Console\Commands;

use App\Infrastructure\Providers\PvaDealsClient;
use Illuminate\Console\Command;

class PvaDealsStatusCommand extends Command
{
    protected $signature = 'pvadeals:status {--services : Also fetch and display available services catalog}';
    protected $description = 'Check PVADeals API health, version, account balance, and configuration';

    public function handle(PvaDealsClient $client): int
    {
        $this->info('====================================================');
        $this->info('       PVADeals v3 API Integration Diagnostics       ');
        $this->info('====================================================');

        $baseUrl = config('services.pvadeals.base_url', 'https://prod-v3.pvadeals.com/v3/api');
        $hasKey = $client->hasApiKey();
        $keyPreview = $hasKey ? substr(config('services.pvadeals.key'), 0, 10) . '...' : 'NONE (Set PVADEALS_API_KEY in .env)';

        $this->line("<comment>Base URL:</comment> {$baseUrl}");
        $this->line("<comment>API Key:</comment>  {$keyPreview}");
        $this->newLine();

        // 1. Health Probe
        $this->info('Checking endpoint health: /v3/api/health');
        $health = $client->checkHealth();
        $status = $health['status'] ?? 'unknown';
        if ($status === 'operational') {
            $this->info("✓ Health: OPERATIONAL (v{$health['version']})");
        } else {
            $this->warn("! Health status: {$status}");
        }

        // 2. Version Probe
        $version = $client->getVersion();
        $this->line("Backend API Version: " . ($version['version'] ?? 'N/A'));
        $this->newLine();

        // 3. Balance Check
        if (!$hasKey) {
            $this->error('No PVADEALS_API_KEY configured in .env. Cannot check balance or make authenticated requests.');
            $this->line('Add your API key to your .env file:');
            $this->line('  PVADEALS_API_KEY=API-xxxxxxxxxxxxxxxxxxxx');
            return Command::SUCCESS;
        }

        $this->info('Checking account balance: /v3/api/balance');
        $balance = $client->getBalance();
        $balanceRes = $client->getRawBalance();

        if ($balance !== null) {
            $this->info("✓ Account Authenticated! Current Balance: \${$balance} USD");
        } else {
            $msg = $balanceRes['message'] ?? 'Unable to retrieve balance';
            $this->error("Failed to retrieve balance: {$msg}");
        }
        $this->newLine();

        // 4. Services (Optional)
        if ($this->option('services')) {
            $this->info('Fetching services catalog: /v3/api/services/all (cached 5m)');
            $services = $client->getAllServices();

            if (empty($services)) {
                $this->warn('No services returned or error occurred.');
            } else {
                $this->info("Found " . count($services) . " services. Showing first 10:");
                $rows = [];
                foreach (array_slice($services, 0, 10) as $s) {
                    $rows[] = [
                        $s['_id'] ?? 'N/A',
                        $s['name'] ?? 'N/A',
                        $s['country'] ?? 'N/A',
                        '$' . ($s['STRprice'] ?? '0.00'),
                        $s['STRstock'] ?? 0,
                        $s['LTRstock'] ?? 0,
                    ];
                }
                $this->table(['ID', 'Service Name', 'Country', 'STR Price', 'STR Stock', 'LTR Stock'], $rows);
            }
        }

        $webhookUrl = url('/api/v1/webhooks/providers/pvadeals');
        $this->info("Configured Webhook URL to set in PVADeals dashboard:");
        $this->line("  <info>{$webhookUrl}</info>");

        return Command::SUCCESS;
    }
}
