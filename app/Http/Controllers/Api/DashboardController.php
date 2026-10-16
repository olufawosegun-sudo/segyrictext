<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Document;
use App\Services\HealthMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mobile Dashboard summary payload.
     */
    public function index(Request $request, HealthMetricsService $healthService): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_documents' => Document::where('user_id', $user->id)->count(),
            'processed_documents' => Document::where('user_id', $user->id)->where('status', 'processed')->count(),
            'pending_documents' => Document::where('user_id', $user->id)->where('status', 'pending_processing')->count(),
            'draft_documents' => Document::where('user_id', $user->id)->where('status', 'draft')->count(),
            'total_words' => Document::where('user_id', $user->id)->sum('word_count'),
        ];

        $recentDocuments = Document::where('user_id', $user->id)
            ->with('category:id,name,color,icon')
            ->latest()
            ->limit(5)
            ->get();

        $categories = Category::where('user_id', $user->id)
            ->orWhereNull('user_id')
            ->withCount(['documents' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get();

        $health = $healthService->getSystemHealth();

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
            'recent_documents' => $recentDocuments,
            'categories' => $categories,
            'system_health' => [
                'database' => $health['database']['status'] ?? 'unknown',
                'queue' => $health['queue']['status'] ?? 'unknown',
                'pending_jobs' => $health['queue']['pending_jobs'] ?? 0,
            ],
        ]);
    }
}
