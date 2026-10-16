<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * List all available online services (WhatsApp, Telegram, Google, etc.).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query()->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
        ]);
    }

    /**
     * Get single service details.
     */
    public function show(int $id): JsonResponse
    {
        $service = Service::where('is_active', true)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }
}
