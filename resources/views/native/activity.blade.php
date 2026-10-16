@extends('native.layouts.mobile')

@section('title', 'Background Activity')
@section('header_title', 'Activity & Cron')

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px;">
    <!-- Live System State Card -->
    <div class="touch-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.1) 100%); border-color: rgba(16, 185, 129, 0.3);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 11px; font-weight: 700; color: #34d399; text-transform: uppercase;">Scheduler & Workers</div>
            <span class="m-badge m-badge-success">Active</span>
        </div>
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-top: 6px;">Background Engine Running</h3>
        <p style="font-size: 12px; color: var(--m-text-muted); margin-top: 4px;">
            Cron tasks execute automatically via Laravel Scheduler.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 14px;">
            <div style="background: rgba(15, 23, 42, 0.6); padding: 10px; border-radius: 10px; border: 1px solid var(--m-border);">
                <div style="font-size: 10px; color: var(--m-text-dim);">Database Latency</div>
                <div style="font-size: 14px; font-weight: 700; color: #34d399; font-family: monospace;">{{ $health['database']['latency_ms'] ?? 0 }}ms</div>
            </div>
            <div style="background: rgba(15, 23, 42, 0.6); padding: 10px; border-radius: 10px; border: 1px solid var(--m-border);">
                <div style="font-size: 10px; color: var(--m-text-dim);">Queue Driver</div>
                <div style="font-size: 14px; font-weight: 700; color: #67e8f9; font-family: monospace;">{{ strtoupper(config('queue.default')) }}</div>
            </div>
        </div>
    </div>

    <!-- Manual Health Check Trigger -->
    <form action="{{ route('web.scheduler.trigger') }}" method="POST">
        @csrf
        <input type="hidden" name="command" value="app:system-health-check">
        <button type="submit" class="m-btn m-btn-secondary" style="font-size: 13px; padding: 12px;">
            ⚡ Run Health Diagnostics Now
        </button>
    </form>

    <!-- Recent Background Executions -->
    <div>
        <div style="font-size: 13px; font-weight: 700; color: var(--m-text-muted); text-transform: uppercase; margin-bottom: 10px;">
            Scheduled Task Logs
        </div>

        @forelse($taskLogs as $log)
            <div class="touch-card" style="padding: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #fff; font-size: 13px;">{{ $log->task_name }}</strong>
                    @if($log->status === 'completed')
                        <span class="m-badge m-badge-success">✓ {{ $log->duration_ms }}ms</span>
                    @else
                        <span class="m-badge m-badge-warning">✗ Failed</span>
                    @endif
                </div>

                <div style="font-size: 11px; color: var(--m-text-dim); margin-top: 4px; font-family: monospace;">
                    {{ $log->command }}
                </div>

                <div style="font-size: 11px; color: var(--m-text-muted); margin-top: 8px; background: rgba(0,0,0,0.3); padding: 8px 10px; border-radius: 8px; border: 1px solid var(--m-border);">
                    {{ Str::limit($log->output ?? $log->error_message ?? 'Executed successfully.', 70) }}
                </div>

                <div style="font-size: 10px; color: var(--m-text-dim); margin-top: 8px; text-align: right;">
                    {{ $log->started_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="touch-card" style="text-align: center; color: var(--m-text-dim); font-size: 13px;">
                No background tasks recorded yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
