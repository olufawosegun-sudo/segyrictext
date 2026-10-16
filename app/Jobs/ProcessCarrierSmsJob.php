<?php

namespace App\Jobs;

use App\Application\Services\SmsIngestionService;
use App\Domain\DTOs\IncomingSmsDTO;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCarrierSmsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public IncomingSmsDTO $smsDto
    ) {}

    public function handle(SmsIngestionService $service): void
    {
        Log::info("ProcessCarrierSmsJob: Processing asynchronous SMS ingestion for [{$this->smsDto->recipient}] from [{$this->smsDto->sender}]");

        try {
            $recordedSms = $service->ingest($this->smsDto);
            Log::info("ProcessCarrierSmsJob: Successfully ingested SMS #{$recordedSms?->id} asynchronously.");
        } catch (\Throwable $e) {
            Log::error("ProcessCarrierSmsJob: Failed to ingest incoming SMS: {$e->getMessage()}", [
                'exception' => $e,
                'recipient' => $this->smsDto->recipient,
                'provider' => $this->smsDto->providerCode,
            ]);
            throw $e;
        }
    }
}
