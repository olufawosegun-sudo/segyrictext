@extends('web.layouts.app')

@section('title', $document->title)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('web.documents.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">{{ $document->title }}</h2>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--text-dim); margin-top: 4px;">
                    <span>Created {{ $document->created_at->format('M d, Y H:i') }}</span>
                    <span>•</span>
                    <span>Origin: <strong style="color: #a5b4fc;">{{ strtoupper($document->origin_platform) }}</strong></span>
                    <span>•</span>
                    <span>Format: <strong style="color: #67e8f9;">{{ strtoupper($document->format) }}</strong></span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <form action="{{ route('web.documents.process', $document) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" title="Instant Analysis">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Re-Analyze
                </button>
            </form>
            <form action="{{ route('web.documents.process', $document) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="async" value="1">
                <button type="submit" class="btn btn-primary btn-sm" title="Dispatch to Queue Worker">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Queue Job
                </button>
            </form>
            <a href="{{ route('web.documents.edit', $document) }}" class="btn btn-secondary btn-sm">Edit</a>
        </div>
    </div>

    <!-- Metrics Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase;">Status</div>
            <div style="margin-top: 8px;">
                @if($document->status === 'processed')
                    <span class="badge badge-success"><span class="status-dot status-dot-green"></span> Processed</span>
                @elseif($document->status === 'pending_processing')
                    <span class="badge badge-warning"><span class="status-dot status-dot-amber"></span> In Queue</span>
                @else
                    <span class="badge badge-info">Draft</span>
                @endif
            </div>
        </div>

        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase;">Word & Char Count</div>
            <div style="font-size: 20px; font-weight: 800; color: #fff; margin-top: 6px; font-family: monospace;">
                {{ $document->word_count }} <span style="font-size: 12px; color: var(--text-dim); font-weight: 400;">words ({{ $document->char_count }} chars)</span>
            </div>
        </div>

        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase;">Est. Reading Time</div>
            <div style="font-size: 20px; font-weight: 800; color: #67e8f9; margin-top: 6px;">
                {{ $document->analysis_metrics['reading_time_mins'] ?? 1 }} min
            </div>
        </div>

        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase;">Sentiment Score</div>
            <div style="font-size: 16px; font-weight: 700; color: #fff; margin-top: 8px;">
                @php $sentiment = $document->analysis_metrics['sentiment'] ?? 'neutral'; @endphp
                @if($sentiment === 'positive')
                    <span class="badge badge-success">Positive</span>
                @elseif($sentiment === 'negative')
                    <span class="badge badge-danger">Negative</span>
                @else
                    <span class="badge badge-primary">Neutral</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Document Content & Keywords Grid -->
    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 24px; align-items: start;">
        <!-- Rendered Text Box -->
        <div class="glass-panel" style="padding: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); margin-bottom: 20px;">
                <span style="font-size: 13px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.05em;">Document Content</span>
                <button onclick="navigator.clipboard.writeText(document.getElementById('rawDocContent').innerText); alert('Content copied to clipboard!');" class="btn btn-secondary btn-sm">
                    Copy Text
                </button>
            </div>

            <div id="rawDocContent" class="font-mono" style="white-space: pre-wrap; line-height: 1.8; color: #e2e8f0; font-size: 14px; background: rgba(15, 23, 42, 0.4); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">{{ $document->content }}</div>
        </div>

        <!-- Keyword Extraction & Metrics Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="glass-panel" style="padding: 22px;">
                <h4 style="font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.05em;">
                    Extracted Keywords
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @if(!empty($document->analysis_metrics['top_keywords']))
                        @foreach($document->analysis_metrics['top_keywords'] as $kw)
                            <span class="badge badge-primary" style="font-size: 12px;">#{{ $kw }}</span>
                        @endforeach
                    @else
                        <span style="font-size: 13px; color: var(--text-dim);">Run analysis to extract keywords.</span>
                    @endif
                </div>
            </div>

            <div class="glass-panel" style="padding: 22px;">
                <h4 style="font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.05em;">
                    Processing Details
                </h4>
                <div style="font-size: 12px; display: flex; flex-direction: column; gap: 10px; color: var(--text-dim);">
                    <div>Processed At: <strong style="color: #fff;">{{ $document->processed_at ? $document->processed_at->toDayDateTimeString() : 'Not yet' }}</strong></div>
                    <div>Paragraphs: <strong style="color: #fff;">{{ $document->analysis_metrics['paragraphs'] ?? 1 }}</strong></div>
                    <div>Origin Platform: <strong style="color: #67e8f9;">{{ ucfirst($document->origin_platform) }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
