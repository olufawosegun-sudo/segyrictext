@extends('web.layouts.app')

@section('title', 'Documents & Texts')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">Documents & Text Library</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Unified text repository shared across Web and Native Mobile clients</p>
        </div>
        <a href="{{ route('web.documents.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Create New Document
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="glass-panel" style="padding: 20px;">
        <form action="{{ route('web.documents.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; margin-bottom: 6px; display: block;">Search Texts</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input">
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; margin-bottom: 6px; display: block;">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; margin-bottom: 6px; display: block;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="pending_processing" {{ request('status') == 'pending_processing' ? 'selected' : '' }}>Queued / Pending</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-secondary" style="height: 44px; width: 100%;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="glass-panel" style="padding: 24px;">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Origin Platform</th>
                        <th>Status</th>
                        <th>Words / Chars</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>
                                <a href="{{ route('web.documents.show', $doc) }}" style="font-weight: 600; color: #fff; text-decoration: none; font-size: 15px;">
                                    {{ $doc->title }}
                                </a>
                                <div style="font-size: 12px; color: var(--text-dim); margin-top: 4px;">
                                    Format: <span style="font-family: monospace; color: #a5b4fc;">{{ strtoupper($doc->format) }}</span>
                                </div>
                            </td>
                            <td>
                                @if($doc->category)
                                    <span style="color: {{ $doc->category->color }}; font-weight: 600; font-size: 13px;">
                                        ● {{ $doc->category->name }}
                                    </span>
                                @else
                                    <span style="color: var(--text-dim);">General</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->origin_platform === 'android')
                                    <span class="badge badge-success">Native Android</span>
                                @elseif($doc->origin_platform === 'ios')
                                    <span class="badge badge-info">Native iOS</span>
                                @elseif($doc->origin_platform === 'cron')
                                    <span class="badge badge-warning">Cron Job</span>
                                @else
                                    <span class="badge badge-primary">Web Browser</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->status === 'processed')
                                    <span class="badge badge-success"><span class="status-dot status-dot-green"></span> Processed</span>
                                @elseif($doc->status === 'pending_processing')
                                    <span class="badge badge-warning"><span class="status-dot status-dot-amber"></span> In Queue</span>
                                @else
                                    <span class="badge badge-info">Draft</span>
                                @endif
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--text-muted);">
                                {{ $doc->word_count }}w / {{ $doc->char_count }}c
                            </td>
                            <td style="font-size: 13px; color: var(--text-dim);">
                                {{ $doc->created_at->format('M d, Y H:i') }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="{{ route('web.documents.show', $doc) }}" class="btn btn-secondary btn-sm">View</a>
                                    <a href="{{ route('web.documents.edit', $doc) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route('web.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 48px;">
                                No documents match your query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection
