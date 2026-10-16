<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\PricingEngineService;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PricingRule;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct(protected PricingEngineService $pricingEngine) {}

    /**
     * Get price for a country and service combination, or full rate card.
     */
    public function index(Request $request): JsonResponse
    {
        $countryId = $request->input('country_id');
        $serviceId = $request->input('service_id');

        if ($countryId && $serviceId) {
            $country = Country::findOrFail($countryId);
            $service = Service::findOrFail($serviceId);

            $user = $request->user();
            $targetCurrency = $request->input('currency')
                ?? $user?->wallet?->currency?->code
                ?? 'NGN';

            $localized = $this->pricingEngine->calculatePriceInCurrency($country, $service, $targetCurrency);

            return response()->json([
                'success' => true,
                'data' => [
                    'country_id' => $country->id,
                    'country_name' => $country->name,
                    'country_code' => $country->iso2,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'price' => $localized['price'],
                    'selling_price' => $localized['price'],
                    'price_usd' => $localized['price_usd'],
                    'currency' => $localized['currency_code'],
                    'currency_code' => $localized['currency_code'],
                    'currency_symbol' => $localized['currency_symbol'],
                    'formatted' => $localized['formatted'],
                ],
            ]);
        }

        $query = PricingRule::query()
            ->with(['country', 'service'])
            ->where('is_active', true);

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $rules = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $rules->items(),
            'pagination' => [
                'total' => $rules->total(),
                'per_page' => $rules->perPage(),
                'current_page' => $rules->currentPage(),
                'last_page' => $rules->lastPage(),
            ],
        ]);
    }
}
