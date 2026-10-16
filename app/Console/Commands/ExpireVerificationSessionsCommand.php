<?php

namespace App\Console\Commands;

use App\Application\Services\VerificationLifecycleService;
use Illuminate\Console\Command;

class ExpireVerificationSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire overdue verification sessions, returning numbers to pool and auto-releasing wallet holds';

    /**
     * Execute the console command.
     */
    public function handle(VerificationLifecycleService $lifecycleService): int
    {
        $this->info('Checking for overdue verification sessions...');
        $count = $lifecycleService->expireOverdueSessions();
        $this->info("Successfully expired {$count} overdue session(s) and refunded holds.");

        return Command::SUCCESS;
    }
}
