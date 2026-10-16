<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Services\TextProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(protected TextProcessingService $textService)
    {
    }

    /**
     * List user documents with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Document::where('user_id', $user->id)
            ->with('category:id,name,color,icon');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($documents);
    }

    /**
     * Create a new document.
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $platform = $request->header('X-Platform', $request->attributes->get('platform', 'mobile'));
        $document = $this->textService->createDocument($request->user(), $request->validated(), $platform);

        return response()->json([
            'status' => 'success',
            'message' => 'Document created successfully',
            'document' => $document->load('category:id,name,color,icon'),
        ], 201);
    }

    /**
     * Show single document with metrics.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $document = Document::where('user_id', $request->user()->id)
            ->with('category')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'document' => $document,
        ]);
    }

    /**
     * Update a document.
     */
    public function update(UpdateDocumentRequest $request, int $id): JsonResponse
    {
        $document = Document::where('user_id', $request->user()->id)->findOrFail($id);
        $updated = $this->textService->updateDocument($document, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Document updated successfully',
            'document' => $updated->load('category'),
        ]);
    }

    /**
     * Trigger immediate or queued re-analysis.
     */
    public function process(Request $request, int $id): JsonResponse
    {
        $document = Document::where('user_id', $request->user()->id)->findOrFail($id);
        
        if ($request->boolean('async', false)) {
            $document->update(['status' => 'pending_processing']);
            \App\Jobs\ProcessTextJob::dispatch($document->id);
            return response()->json([
                'status' => 'queued',
                'message' => 'Processing job dispatched to background queue',
                'document' => $document,
            ]);
        }

        $metrics = $this->textService->analyzeText($document);

        return response()->json([
            'status' => 'success',
            'message' => 'Document processed successfully',
            'metrics' => $metrics,
            'document' => $document->fresh(),
        ]);
    }

    /**
     * Delete document (soft delete).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $document = Document::where('user_id', $request->user()->id)->findOrFail($id);
        $document->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Document deleted successfully',
        ]);
    }
}
