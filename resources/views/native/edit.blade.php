@extends('native.layouts.mobile')

@section('title', 'Edit Text')
@section('header_title', 'Edit Text')
@section('back_url', route('mobile.show', $document))

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px;">
    <form action="{{ route('mobile.update', $document) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 14px;">
            <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Title</label>
            <input type="text" name="title" value="{{ old('title', $document->title) }}" required class="m-input">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Category</label>
                <select name="category_id" class="m-select">
                    <option value="">General</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $document->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Format</label>
                <select name="format" class="m-select">
                    <option value="markdown" {{ old('format', $document->format) == 'markdown' ? 'selected' : '' }}>Markdown</option>
                    <option value="plain" {{ old('format', $document->format) == 'plain' ? 'selected' : '' }}>Plain Text</option>
                    <option value="html" {{ old('format', $document->format) == 'html' ? 'selected' : '' }}>HTML</option>
                    <option value="json" {{ old('format', $document->format) == 'json' ? 'selected' : '' }}>JSON</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="font-size: 12px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Content</label>
            <textarea name="content" rows="11" required class="m-textarea font-mono">{{ old('content', $document->content) }}</textarea>
        </div>

        <div class="touch-card" style="margin-bottom: 20px; padding: 14px;">
            <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #fff;">Queue Background Analysis</div>
                    <div style="font-size: 12px; color: var(--m-text-dim);">Recalculate metrics via queue worker</div>
                </div>
                <input type="checkbox" name="reprocess" value="1" style="width: 20px; height: 20px; accent-color: #6366f1;">
            </label>
        </div>

        <button type="submit" class="m-btn m-btn-primary">
            Save Changes
        </button>
    </form>
</div>
@endsection
