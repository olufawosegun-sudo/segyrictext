@extends('native.layouts.mobile')

@section('title', 'Library')
@section('header_title', 'Library')

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px;">
    <!-- Search Bar -->
    <form action="{{ route('mobile.library') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" class="m-input" style="margin-top: 0;">
    </form>

    <!-- Category Filter Pills -->
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
        <a href="{{ route('mobile.library') }}" class="m-badge {{ !request('category') ? 'm-badge-primary' : 'm-badge-info' }}" style="padding: 6px 12px; font-size: 12px; text-decoration: none; flex-shrink: 0;">
            All ({{ $documents->total() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('mobile.library', ['category' => $cat->id]) }}" class="m-badge {{ request('category') == $cat->id ? 'm-badge-primary' : '' }}" style="padding: 6px 12px; font-size: 12px; text-decoration: none; flex-shrink: 0; background: var(--m-surface); border: 1px solid var(--m-border); color: #fff;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Document Items -->
    <div>
        @forelse($documents as $doc)
            <a href="{{ route('mobile.show', $doc) }}" class="touch-card">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #fff;">{{ $doc->title }}</h3>
                    @if($doc->status === 'processed')
                        <span class="m-badge m-badge-success">✓ Processed</span>
                    @elseif($doc->status === 'pending_processing')
                        <span class="m-badge m-badge-warning">In Queue</span>
                    @else
                        <span class="m-badge m-badge-info">Draft</span>
                    @endif
                </div>

                <p style="font-size: 13px; color: var(--m-text-muted); margin-top: 6px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ strip_tags($doc->content) }}
                </p>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 12px; font-size: 11px; color: var(--m-text-dim);">
                    <span style="color: #67e8f9;">{{ strtoupper($doc->format) }}</span>
                    <span>{{ $doc->word_count }} words • {{ $doc->created_at->format('M d') }}</span>
                </div>
            </a>
        @empty
            <div class="touch-card" style="text-align: center; padding: 40px 16px;">
                <p style="color: var(--m-text-dim); font-size: 14px;">No documents found.</p>
                <a href="{{ route('mobile.editor') }}" class="m-btn m-btn-primary" style="margin-top: 16px; display: inline-flex; width: auto; font-size: 13px; padding: 10px 20px;">
                    + Write New Text
                </a>
            </div>
        @endforelse
    </div>

    <div>
        {{ $documents->links() }}
    </div>
</div>
@endsection
