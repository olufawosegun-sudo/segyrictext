@extends('native.layouts.mobile')

@section('title', 'New Text')
@section('header_title', 'Write Text')
@section('back_url', route('mobile.home'))

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px;">
    <form action="{{ route('mobile.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 14px;">
            <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="m-input">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Category</label>
                <select name="category_id" class="m-select">
                    <option value="">General</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Format</label>
                <select name="format" class="m-select">
                    <option value="markdown">Markdown</option>
                    <option value="plain">Plain Text</option>
                    <option value="html">HTML</option>
                    <option value="json">JSON</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Content</label>
                <span id="mCount" style="font-size: 11px; color: var(--m-text-dim); font-family: monospace;">0 words</span>
            </div>
            <textarea name="content" id="mText" rows="11" required class="m-textarea font-mono" oninput="document.getElementById('mCount').innerText = (this.value.trim() ? this.value.trim().split(/\s+/).length : 0) + ' words'">{{ old('content') }}</textarea>
        </div>

        <!-- Background Queue Toggle -->
        <div class="touch-card" style="margin-bottom: 20px; padding: 14px;">
            <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #fff;">Async Queue Processing</div>
                    <div style="font-size: 12px; color: var(--m-text-dim);">Execute analysis in background queue worker</div>
                </div>
                <input type="checkbox" name="queue_processing" value="1" checked style="width: 20px; height: 20px; accent-color: #6366f1;">
            </label>
        </div>

        <button type="submit" class="m-btn m-btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save & Process Document
        </button>
    </form>
</div>
@endsection
