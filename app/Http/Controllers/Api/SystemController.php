<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduledTaskLog;
use App\Services\HealthMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    /**
     * System diagnostic endpoint.
     */
    public function health(HealthMetricsService $service): JsonResponse
    {
        return response()->json($service->getSystemHealth());
    }

    /**
     * Recent scheduled task logs for mobile audit view.
     */
    public function taskLogs(Request $request): JsonResponse
    {
        $logs = ScheduledTaskLog::latest()
            ->limit($request->input('limit', 20))
            ->get();

        return response()->json([
            'status' => 'success',
            'logs' => $logs,
        ]);
    }

    /**
     * Manually trigger a scheduled command from mobile.
     */
    public function triggerTask(Request $request): JsonResponse
    {
        $command = $request->validate([
            'command' => ['required', 'string', 'in:app:system-health-check,app:process-pending-texts,app:send-daily-digest,app:cleanup-expired-tokens,app:prune-old-records'],
        ])['command'];

        $exitCode = Artisan::call($command);
        $output = Artisan::output();

        return response()->json([
            'status' => $exitCode === 0 ? 'success' : 'failed',
            'command' => $command,
            'exit_code' => $exitCode,
            'output' => $output,
        ]);
    }
}
