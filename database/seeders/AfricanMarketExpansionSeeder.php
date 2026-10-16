<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\PricingRule;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class AfricanMarketExpansionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. African Currencies (ISO 4217 3-letter codes)
        $currencies = [
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true, 'is_default' => false],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh', 'decimal_places' => 2, 'is_active' => true, 'is_default' => false],
            ['name' => 'Ghanaian Cedi', 'code' => 'GHS', 'symbol' => 'GH₵', 'decimal_places' => 2, 'is_active' => true, 'is_default' => false],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'symbol' => 'R', 'decimal_places' => 2, 'is_active' => true, 'is_default' => false],
            ['name' => 'Ugandan Shilling', 'code' => 'UGX', 'symbol' => 'USh', 'decimal_places' => 0, 'is_active' => true, 'is_default' => false],
        ];

        foreach ($currencies as $c) {
            Currency::firstOrCreate(['code' => $c['code']], $c);
        }

        // 2. High-Demand Foreign Services for African Freelancers & Creators
        $categoryFintech = ServiceCategory::firstOrCreate(
            ['name' => 'Fintech & Banking'],
            ['name' => 'Fintech & Banking', 'description' => 'Global payment gateways, merchant portals and international banks', 'is_active' => true]
        );

        $services = [
            ['name' => 'PayPal', 'code' => 'paypal', 'description' => 'International freelance payments & foreign merchant accounts', 'category' => $categoryFintech],
            ['name' => 'Apple ID / iCloud', 'code' => 'apple', 'description' => 'US/UK App Store accounts and iOS creator activation', 'category' => null],
            ['name' => 'Stripe', 'code' => 'stripe', 'description' => 'Global merchant checkout & payment gateway registration', 'category' => $categoryFintech],
            ['name' => 'Wise', 'code' => 'wise', 'description' => 'Multi-currency accounts & international wire verification', 'category' => $categoryFintech],
            ['name' => 'Binance', 'code' => 'binance', 'description' => 'Crypto exchange P2P & security confirmation', 'category' => $categoryFintech],
        ];

        $serviceModels = [];
        foreach ($services as $s) {
            $cat = $s['category'];
            unset($s['category']);
            $service = Service::firstOrCreate(['code' => $s['code']], array_merge($s, ['is_active' => true]));
            $serviceModels[$s['code']] = $service;

            if ($cat) {
                $cat->services()->syncWithoutDetaching([$service->id]);
            }
        }

        // 3. Pricing for Top Foreign Destinations (US, UK, CA, FR, DE, NL)
        $foreignCountries = Country::whereIn('iso2', ['US', 'GB', 'CA', 'FR', 'DE', 'NL'])->get();

        $rates = [
            'paypal' => ['purchase' => 0.60, 'selling' => 1.25],
            'apple' => ['purchase' => 0.45, 'selling' => 0.95],
            'stripe' => ['purchase' => 0.65, 'selling' => 1.35],
            'wise' => ['purchase' => 0.55, 'selling' => 1.15],
            'binance' => ['purchase' => 0.40, 'selling' => 0.85],
        ];

        foreach ($foreignCountries as $country) {
            foreach ($rates as $srvCode => $price) {
                if (isset($serviceModels[$srvCode])) {
                    PricingRule::firstOrCreate(
                        [
                            'country_id' => $country->id,
                            'service_id' => $serviceModels[$srvCode]->id,
                        ],
                        [
                            'purchase_price' => $price['purchase'],
                            'selling_price' => $price['selling'],
                            'currency_code' => 'USD',
                            'priority' => 1,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
