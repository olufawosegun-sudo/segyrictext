@extends('web.layouts.app')

@section('title', 'Scheduler & Cron Jobs')

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">Laravel Scheduler & Cron Control Center</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Manage automated background recurring tasks, queue workers, and execution audit trails</p>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <form action="{{ route('web.scheduler.clear-logs') }}" method="POST" onsubmit="return confirm('Clear all execution audit logs?');" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Clear Audit Logs</button>
            </form>
        </div>
    </div>

    <!-- Production Cron Configuration Box -->
    <div class="glass-panel" style="padding: 24px; background: rgba(15, 23, 42, 0.9); border-left: 4px solid #6366f1;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Production Server Cron Configuration
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 6px;">
                    To run all scheduled tasks automatically every minute on your Linux/cPanel/VPS production server, add this single entry to your server's crontab:
                </p>
                <div style="margin-top: 12px; background: #000; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #34d399; user-select: all;">
                    * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <span class="badge badge-success">Queue: {{ strtoupper(config('queue.default')) }}</span>
                <span class="badge badge-info">Pending Jobs: {{ $pendingJobs }}</span>
                <span class="badge badge-{{ $failedJobs > 0 ? 'danger' : 'primary' }}">Failed Jobs: {{ $failedJobs }}</span>
            </div>
        </div>
    </div>

    <!-- Scheduled Tasks List -->
    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 20px;">
            Configured Scheduled Tasks ({{ count($scheduledTasks) }})
        </h3>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($scheduledTasks as $task)
                <div class="glass-card" style="padding: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.15); color: #818cf8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <h4 style="font-size: 15px; font-weight: 700; color: #fff;">{{ $task['name'] }}</h4>
                                <span class="badge badge-primary font-mono" style="font-size: 11px;">{{ $task['schedule'] }}</span>
                            </div>
                            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">{{ $task['description'] }}</p>
                            <div style="margin-top: 6px; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #67e8f9;">
                                Command: php artisan {{ $task['command'] }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('web.scheduler.trigger') }}" method="POST">
                            @csrf
                            <input type="hidden" name="command" value="{{ $task['command'] }}">
                            <button type="submit" class="btn btn-secondary btn-sm" style="border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Execute Now
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Scheduled Task Audit Logs Table -->
    <div class="glass-panel" style="padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #fff;">Execution Audit Trail</h3>
                <p style="font-size: 13px; color: var(--text-dim);">Detailed execution history, latency (ms), exit status, and records processed</p>
            </div>
            <span class="badge badge-info">{{ $taskLogs->total() }} Total Recorded Executions</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Output / Summary</th>
                        <th>Executed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taskLogs as $log)
                        <tr>
                            <td>
                                <strong style="color: #fff; font-size: 14px;">{{ $log->task_name }}</strong>
                                <div style="font-family: monospace; font-size: 11px; color: var(--text-dim); margin-top: 2px;">
                                    {{ $log->command }}
                                </div>
                            </td>
                            <td>
                                @if($log->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($log->status === 'running')
                                    <span class="badge badge-warning">Running</span>
                                @else
                                    <span class="badge badge-danger">Failed</span>
                                @endif
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--text-muted);">
                                {{ $log->duration_ms }}ms
                            </td>
                            <td style="max-width: 380px;">
                                @if($log->error_message)
                                    <span style="color: #f87171; font-size: 12px;">{{ Str::limit($log->error_message, 80) }}</span>
                                @else
                                    <span style="color: var(--text-main); font-size: 12px;">{{ Str::limit($log->output, 80) }}</span>
                                @endif
                            </td>
                            <td style="font-size: 13px; color: var(--text-dim); white-space: nowrap;">
                                {{ $log->started_at->format('M d, Y H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-dim); padding: 32px;">
                                No scheduled task executions recorded yet. Click "Execute Now" on any task above to run it!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $taskLogs->links() }}
        </div>
    </div>
</div>
@endsection
