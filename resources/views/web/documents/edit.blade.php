@extends('web.layouts.app')

@section('title', 'Edit Document')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">Edit Document</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Update document content and trigger re-analysis</p>
        </div>
        <a href="{{ route('web.documents.show', $document) }}" class="btn btn-secondary btn-sm">
            ← Back to Document
        </a>
    </div>

    <div class="glass-panel" style="padding: 32px;">
        <form action="{{ route('web.documents.update', $document) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Document Title</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}" required class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">General (No Category)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $document->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Content Format</label>
                    <select name="format" class="form-select">
                        <option value="markdown" {{ old('format', $document->format) == 'markdown' ? 'selected' : '' }}>Markdown</option>
                        <option value="plain" {{ old('format', $document->format) == 'plain' ? 'selected' : '' }}>Plain Text</option>
                        <option value="html" {{ old('format', $document->format) == 'html' ? 'selected' : '' }}>HTML</option>
                        <option value="json" {{ old('format', $document->format) == 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Content Body</label>
                <textarea name="content" rows="12" required class="form-textarea font-mono" style="line-height: 1.6;">{{ old('content', $document->content) }}</textarea>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-color); margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="reprocess" value="1" style="width: 18px; height: 18px; accent-color: #6366f1;">
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #fff;">Queue Background Re-Processing</div>
                        <div style="font-size: 12px; color: var(--text-dim); margin-top: 2px;">Recalculate reading time, keywords, and sentiment via background queue worker.</div>
                    </div>
                </label>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 14px;">
                <a href="{{ route('web.documents.show', $document) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    Update Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
