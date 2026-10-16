<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Services\TextProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(protected TextProcessingService $textService)
    {
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Document::where('user_id', $user->id)->with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('web.documents.index', compact('documents', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('user_id', Auth::id())->orWhereNull('user_id')->get();
        return view('web.documents.create', compact('categories'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $platform = $request->attributes->get('platform', 'web');
        $document = $this->textService->createDocument(Auth::user(), $request->validated(), $platform);

        $msg = $document->status === 'pending_processing'
            ? 'Document created and queued for background analysis!'
            : 'Document created successfully!';

        return redirect()->route('web.documents.show', $document)->with('success', $msg);
    }

    public function show(int $id): View
    {
        $document = Document::where('user_id', Auth::id())
            ->with('category')
            ->findOrFail($id);

        return view('web.documents.show', compact('document'));
    }

    public function edit(int $id): View
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);
        $categories = Category::where('user_id', Auth::id())->orWhereNull('user_id')->get();

        return view('web.documents.edit', compact('document', 'categories'));
    }

    public function update(UpdateDocumentRequest $request, int $id): RedirectResponse
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);
        $this->textService->updateDocument($document, $request->validated());

        return redirect()->route('web.documents.show', $document)->with('success', 'Document updated successfully!');
    }

    public function process(Request $request, int $id): RedirectResponse
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);

        if ($request->boolean('async', false)) {
            $document->update(['status' => 'pending_processing']);
            \App\Jobs\ProcessTextJob::dispatch($document->id);
            return back()->with('success', 'Processing job dispatched to the background queue!');
        }

        $this->textService->analyzeText($document);
        return back()->with('success', 'Document analyzed immediately!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);
        $document->delete();

        return redirect()->route('web.documents.index')->with('success', 'Document moved to trash.');
    }
}
