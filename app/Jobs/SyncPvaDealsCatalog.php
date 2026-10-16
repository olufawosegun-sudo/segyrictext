<?php

namespace App\Jobs;

use App\Services\PvaDeals\PvaDealsSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPvaDealsCatalog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** The number of times the job may be attempted. */
    public int $tries = 2;

    /** The number of seconds the job can run before timing out. */
    public int $timeout = 180;

    public function __construct(public ?array $overrideCatalog = null) {}

    public function handle(PvaDealsSyncService $syncService): void
    {
        Log::info("Starting SyncPvaDealsCatalog background job...");
        $result = $syncService->sync($this->overrideCatalog);

        if (!empty($result['success'])) {
            Log::info("SyncPvaDealsCatalog completed successfully.", $result);
        } else {
            Log::warning("SyncPvaDealsCatalog ended with error.", $result);
        }
    }
}
