@extends('web.layouts.app')

@section('title', 'Create Document')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">Create New Document</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Enter text for real-time storage and background queue analysis</p>
        </div>
        <a href="{{ route('web.documents.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Library
        </a>
    </div>

    @if($errors->any())
        <div style="padding: 14px 20px; border-radius: 12px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-panel" style="padding: 32px;">
        <form action="{{ route('web.documents.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Document Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">General (No Category)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: block;">Content Format</label>
                    <select name="format" class="form-select">
                        <option value="markdown" {{ old('format') == 'markdown' ? 'selected' : '' }}>Markdown</option>
                        <option value="plain" {{ old('format') == 'plain' ? 'selected' : '' }}>Plain Text</option>
                        <option value="html" {{ old('format') == 'html' ? 'selected' : '' }}>HTML</option>
                        <option value="json" {{ old('format') == 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-main);">Content Body</label>
                    <span style="font-size: 12px; color: var(--text-dim); font-family: monospace;" id="charCounter">0 words | 0 characters</span>
                </div>
                <textarea name="content" id="contentText" rows="12" required class="form-textarea font-mono" style="line-height: 1.6;" oninput="updateCounters()">{{ old('content') }}</textarea>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-color); margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="queue_processing" value="1" {{ old('queue_processing', '1') == '1' ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #6366f1;">
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #fff;">Dispatch to Asynchronous Queue Worker</div>
                        <div style="font-size: 12px; color: var(--text-dim); margin-top: 2px;">Processes deep metrics, sentiment analysis, reading time, and keyword extraction in the background without blocking the UI response.</div>
                    </div>
                </label>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 14px;">
                <a href="{{ route('web.documents.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save & Dispatch
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateCounters() {
        const text = document.getElementById('contentText').value.trim();
        const chars = text.length;
        const words = text ? text.split(/\s+/).length : 0;
        document.getElementById('charCounter').textContent = `${words} words | ${chars} characters`;
    }
    updateCounters();
</script>
@endsection
