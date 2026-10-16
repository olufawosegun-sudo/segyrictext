<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\TextProcessingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTextJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $documentId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(TextProcessingService $service): void
    {
        $document = Document::find($this->documentId);

        if (!$document) {
            Log::warning("ProcessTextJob: Document #{$this->documentId} not found, skipping.");
            return;
        }

        try {
            $service->analyzeText($document);
            Log::info("ProcessTextJob: Successfully processed Document #{$document->id} ({$document->title})");
        } catch (\Throwable $e) {
            Log::error("ProcessTextJob: Failed processing Document #{$this->documentId}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessTextJob: Job permanently failed for Document #{$this->documentId}: {$exception->getMessage()}");
    }
}
