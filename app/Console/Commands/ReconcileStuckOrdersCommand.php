<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileOrder;
use App\Jobs\SyncOrder;
use App\Models\NumberOrder;
use Illuminate\Console\Command;

class ReconcileStuckOrdersCommand extends Command
{
    protected $signature = 'orders:reconcile-stuck';
    protected $description = 'Safety net scheduler to reconcile stuck placing/needs_review orders and sync pending orders';

    public function handle(): int
    {
        // 1. Placing orders stuck for > 3 minutes (e.g. worker crashed mid-execution)
        $stuckPlacing = NumberOrder::where('status', 'placing')
            ->where('updated_at', '<=', now()->subMinutes(3))
            ->get();

        foreach ($stuckPlacing as $order) {
            $order->update(['status' => 'needs_review']);
            ReconcileOrder::dispatch($order->id);
            $this->info("Dispatched ReconcileOrder for stuck placing order #{$order->id}");
        }

        // 2. Needs review orders stuck for > 2 minutes
        $needsReview = NumberOrder::where('status', 'needs_review')
            ->where('updated_at', '<=', now()->subMinutes(2))
            ->get();

        foreach ($needsReview as $order) {
            ReconcileOrder::dispatch($order->id);
            $this->info("Dispatched ReconcileOrder for needs_review order #{$order->id}");
        }

        // 3. Provider pending orders stuck for > 3 minutes without active sync
        $stuckPending = NumberOrder::where('status', 'provider_pending')
            ->where('updated_at', '<=', now()->subMinutes(3))
            ->get();

        foreach ($stuckPending as $order) {
            SyncOrder::dispatch($order->id);
            $this->info("Dispatched SyncOrder for stuck pending order #{$order->id}");
        }

        return self::SUCCESS;
    }
}
