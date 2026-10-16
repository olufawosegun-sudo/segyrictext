<?php

namespace App\Console\Commands;

use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PvaDealsSyncCommand extends Command
{
    protected $signature = 'pvadeals:sync {--force : Force refresh and bypass cache}';
    protected $description = 'Sync all live services and pricing from PVADeals API into database';

    public function handle(\App\Services\PvaDeals\PvaDealsSyncService $syncService, PvaDealsClient $client): int
    {
        $this->info('Starting PVADeals Services, Countries & Stock Sync...');

        if (!$client->hasApiKey()) {
            $this->error('PVADeals API Key is not configured. Set PVADEALS_API_KEY in .env');
            return Command::FAILURE;
        }

        $this->info('Fetching live catalog and stock from PVADeals API...');
        $result = $syncService->sync();

        if (empty($result['success'])) {
            $this->error('Failed to sync PVADeals catalog: ' . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✓ Successfully synced PVADeals catalog and numbers stock!");
        $this->line("  • Countries Synced: " . ($result['countries_count'] ?? 0));
        $this->line("  • Services Synced:  " . ($result['services_count'] ?? 0));
        $this->line("  • New Created:      " . ($result['created_count'] ?? 0));
        $this->line("  • Records Updated:  " . ($result['updated_count'] ?? 0));
        $this->line("  • Synced At:        " . ($result['synced_at'] ?? now()));

        $totalStock = (int) \App\Models\CountryService::where('is_active', true)->sum('str_stock');
        $this->info("  • Total Available Numbers (STR Stock): " . number_format($totalStock));

        return Command::SUCCESS;
    }
}
