<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * List all available countries.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Country::query()->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso2', 'like', "%{$search}%")
                  ->orWhere('iso3', 'like', "%{$search}%")
                  ->orWhere('dial_code', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => CountryResource::collection($countries),
        ]);
    }

    /**
     * Get single country details.
     */
    public function show(int $id): JsonResponse
    {
        $country = Country::where('is_active', true)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CountryResource($country),
        ]);
    }
}
