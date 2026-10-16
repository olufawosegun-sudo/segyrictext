<?php

namespace App\Services;

use App\Jobs\ProcessTextJob;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Str;

class TextProcessingService
{
    /**
     * Create and optionally queue a document for background processing.
     */
    public function createDocument(User $user, array $data, string $platform = 'web'): Document
    {
        $document = new Document([
            'user_id' => $user->id,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'],
            'format' => $data['format'] ?? 'markdown',
            'status' => ($data['queue_processing'] ?? false) ? 'pending_processing' : 'draft',
            'origin_platform' => $platform,
            'tags' => $data['tags'] ?? [],
            'scheduled_at' => isset($data['scheduled_at']) ? \Carbon\Carbon::parse($data['scheduled_at']) : null,
        ]);

        $document->save();

        if ($document->status === 'pending_processing') {
            ProcessTextJob::dispatch($document->id);
        }

        return $document;
    }

    /**
     * Update a document and optionally queue analysis.
     */
    public function updateDocument(Document $document, array $data): Document
    {
        $document->fill([
            'title' => $data['title'] ?? $document->title,
            'category_id' => array_key_exists('category_id', $data) ? $data['category_id'] : $document->category_id,
            'content' => $data['content'] ?? $document->content,
            'format' => $data['format'] ?? $document->format,
            'tags' => $data['tags'] ?? $document->tags,
            'scheduled_at' => isset($data['scheduled_at']) ? \Carbon\Carbon::parse($data['scheduled_at']) : $document->scheduled_at,
        ]);

        if (!empty($data['reprocess'])) {
            $document->status = 'pending_processing';
            $document->save();
            ProcessTextJob::dispatch($document->id);
        } else {
            $document->save();
        }

        return $document;
    }

    /**
     * Synchronous or asynchronous deep text analysis.
     */
    public function analyzeText(Document $document): array
    {
        $text = strip_tags($document->content ?? '');
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $wordCount = count($words);
        $charCount = mb_strlen($text);
        
        // Reading time estimation (avg 200 words per min)
        $readingTimeMinutes = max(1, (int) ceil($wordCount / 200));

        // Keyword extraction (frequencies)
        $stopWords = ['the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it', 'on', 'you', 'this', 'for', 'with'];
        $frequencyMap = [];
        foreach ($words as $word) {
            $cleaned = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $word));
            if (strlen($cleaned) > 2 && !in_array($cleaned, $stopWords)) {
                $frequencyMap[$cleaned] = ($frequencyMap[$cleaned] ?? 0) + 1;
            }
        }
        arsort($frequencyMap);
        $topKeywords = array_slice(array_keys($frequencyMap), 0, 8);

        // Simple sentiment heuristic
        $positiveWords = ['great', 'good', 'excellent', 'amazing', 'happy', 'fast', 'secure', 'reliable', 'success', 'benefit'];
        $negativeWords = ['bad', 'error', 'failed', 'issue', 'problem', 'slow', 'vulnerable', 'risk', 'bug', 'crash'];
        $posCount = 0;
        $negCount = 0;
        foreach ($words as $word) {
            $w = strtolower($word);
            if (in_array($w, $positiveWords)) $posCount++;
            if (in_array($w, $negativeWords)) $negCount++;
        }

        $sentiment = 'neutral';
        if ($posCount > $negCount) $sentiment = 'positive';
        elseif ($negCount > $posCount) $sentiment = 'negative';

        $metrics = [
            'word_count' => $wordCount,
            'char_count' => $charCount,
            'reading_time_mins' => $readingTimeMinutes,
            'sentiment' => $sentiment,
            'top_keywords' => $topKeywords,
            'paragraphs' => count(explode("\n\n", trim($document->content))),
            'analyzed_at' => now()->toIso8601String(),
        ];

        $document->update([
            'analysis_metrics' => $metrics,
            'word_count' => $wordCount,
            'char_count' => $charCount,
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return $metrics;
    }

    /**
     * Process pending scheduled or queued documents.
     */
    public function processPendingScheduled(): int
    {
        $dueDocuments = Document::where('status', 'pending_processing')
            ->orWhere(function ($query) {
                $query->whereNotNull('scheduled_at')
                      ->where('scheduled_at', '<=', now())
                      ->where('status', 'draft');
            })
            ->limit(50)
            ->get();

        $processed = 0;
        foreach ($dueDocuments as $doc) {
            ProcessTextJob::dispatch($doc->id);
            $processed++;
        }

        return $processed;
    }
}
