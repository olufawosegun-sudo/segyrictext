@extends('native.layouts.mobile')

@section('title', $document->title)
@section('header_title', 'Text Details')
@section('back_url', route('mobile.library'))

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px;">
    <!-- Title & Status Header Card -->
    <div class="touch-card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #fff; line-height: 1.3;">{{ $document->title }}</h2>
            @if($document->status === 'processed')
                <span class="m-badge m-badge-success">✓ Processed</span>
            @elseif($document->status === 'pending_processing')
                <span class="m-badge m-badge-warning">Queued</span>
            @else
                <span class="m-badge m-badge-info">Draft</span>
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px; font-size: 12px; color: var(--m-text-dim);">
            <span>{{ $document->category ? $document->category->name : 'General' }}</span>
            <span>•</span>
            <span>Origin: <strong style="color: #a5b4fc;">{{ strtoupper($document->origin_platform) }}</strong></span>
            <span>•</span>
            <span>{{ $document->created_at->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- Quick Metrics Strip -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
        <div class="touch-card" style="padding: 12px; margin-bottom: 0; text-align: center;">
            <div style="font-size: 10px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Words</div>
            <div style="font-size: 18px; font-weight: 800; color: #fff; margin-top: 4px; font-family: monospace;">{{ $document->word_count }}</div>
        </div>
        <div class="touch-card" style="padding: 12px; margin-bottom: 0; text-align: center;">
            <div style="font-size: 10px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Read Time</div>
            <div style="font-size: 18px; font-weight: 800; color: #67e8f9; margin-top: 4px;">{{ $document->analysis_metrics['reading_time_mins'] ?? 1 }}m</div>
        </div>
        <div class="touch-card" style="padding: 12px; margin-bottom: 0; text-align: center;">
            <div style="font-size: 10px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Sentiment</div>
            <div style="font-size: 13px; font-weight: 700; color: #34d399; margin-top: 6px; text-transform: capitalize;">{{ $document->analysis_metrics['sentiment'] ?? 'Neutral' }}</div>
        </div>
    </div>

    <!-- Keywords Pill Box -->
    @if(!empty($document->analysis_metrics['top_keywords']))
        <div class="touch-card">
            <div style="font-size: 11px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase; margin-bottom: 8px;">Keywords</div>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                @foreach($document->analysis_metrics['top_keywords'] as $kw)
                    <span class="m-badge m-badge-primary" style="font-size: 11px;">#{{ $kw }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Content Box -->
    <div class="touch-card" style="padding: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--m-text-dim); text-transform: uppercase;">Body</span>
            <button onclick="navigator.clipboard.writeText(document.getElementById('mDocBody').innerText); alert('Copied to clipboard!');" style="background: rgba(255,255,255,0.08); border: 1px solid var(--m-border); color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                📋 Copy
            </button>
        </div>
        <div id="mDocBody" class="font-mono" style="white-space: pre-wrap; font-size: 14px; line-height: 1.7; color: #e2e8f0;">{{ $document->content }}</div>
    </div>

    <!-- Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px;">
        <form action="{{ route('mobile.process', $document) }}" method="POST">
            @csrf
            <button type="submit" class="m-btn m-btn-secondary" style="font-size: 13px; padding: 12px;">
                ⚡ Analyze
            </button>
        </form>
        <a href="{{ route('mobile.edit', $document) }}" class="m-btn m-btn-primary" style="font-size: 13px; padding: 12px;">
            ✏️ Edit Text
        </a>
    </div>
</div>
@endsection
