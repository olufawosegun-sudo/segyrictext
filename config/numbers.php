<?php

return [
    /*
    |--------------------------------------------------------------------------
    | All-Services Country Rental Plans (PVADeals API v3)
    |--------------------------------------------------------------------------
    |
    | Defines the allowed countries, labels, flags, carriers, and duration
    | rental plans with their respective USD wholesale prices.
    | Sourced from official PVADeals API documentation (October 2026).
    |
    | Countries offered in this app:
    | - US: 28 days -> $12.99 (always 28; omit duration on purchase)
    | - UK: 30 days -> $6.99, 90 days -> $11.99 ("GB" is an alias)
    | - Hong Kong (HK): 30 days -> $11.50, 180 days -> $22.50 [Badge: New]
    | - Indonesia (ID): 30 days -> $11.99, 180 days -> $21.99 [Badge: New]
    | - Australia (AU): 30 days -> $22.99 only [Badge: New]
    |
    | Canada is NOT offered in this app.
    | This file is the SINGLE SOURCE OF TRUTH for all-services countries.
    |
    */
    'all_services_countries' => [
        'US' => [
            'code' => 'US',
            'aliases' => ['USA'],
            'name' => 'United States',
            'flag' => '🇺🇸',
            'dial_code' => '+1',
            'new' => false,
            'carrier' => [
                'name' => 'T-Mobile',
                'badge' => 'T',
                'color' => '#E20074',
            ],
            'default_duration' => 28,
            'allowed_durations' => [28],
            'plans' => [
                28 => [
                    'duration' => 28,
                    'label' => '28 days',
                    'price_usd' => 12.99,
                    'description' => 'Unlimited SMS for 28 days across all US services',
                ],
            ],
            'supports_per_service' => true,
            'supports_auto_renew' => true,
            'auto_renew_lockout_days' => 0,
            'supports_inventory' => true,
        ],

        'UK' => [
            'code' => 'UK',
            'aliases' => ['GB', 'GBR'],
            'name' => 'United Kingdom',
            'flag' => '🇬🇧',
            'dial_code' => '+44',
            'new' => false,
            'carrier' => [
                'name' => 'EE',
                'badge' => 'EE',
                'color' => '#007B85',
            ],
            'default_duration' => 30,
            'allowed_durations' => [30, 90],
            'plans' => [
                30 => [
                    'duration' => 30,
                    'label' => '30 days',
                    'price_usd' => 6.99,
                    'description' => 'Unlimited UK SMS for 30 days across all websites',
                ],
                90 => [
                    'duration' => 90,
                    'label' => '90 days',
                    'price_usd' => 11.99,
                    'description' => 'Extended 90-day UK line across all websites',
                ],
            ],
            'supports_per_service' => true,
            'supports_auto_renew' => true,
            'auto_renew_lockout_days' => 4,
            'supports_inventory' => false,
        ],

        'HK' => [
            'code' => 'HK',
            'aliases' => ['HKG', 'HONG KONG'],
            'name' => 'Hong Kong',
            'flag' => '🇭🇰',
            'dial_code' => '+852',
            'new' => true,
            'carrier' => [
                'name' => 'HK Telecom',
                'badge' => 'HK',
                'color' => '#E11D48',
            ],
            'default_duration' => 30,
            'allowed_durations' => [30, 180],
            'plans' => [
                30 => [
                    'duration' => 30,
                    'label' => '30 days',
                    'price_usd' => 11.50,
                    'description' => '30-day Hong Kong all-services virtual line',
                ],
                180 => [
                    'duration' => 180,
                    'label' => '180 days',
                    'price_usd' => 22.50,
                    'description' => 'Half-year 180-day Hong Kong line across all services',
                ],
            ],
            'supports_per_service' => false,
            'supports_auto_renew' => true,
            'auto_renew_lockout_days' => 4,
            'supports_inventory' => false,
        ],

        'ID' => [
            'code' => 'ID',
            'aliases' => ['IDN', 'INDONESIA'],
            'name' => 'Indonesia',
            'flag' => '🇮🇩',
            'dial_code' => '+62',
            'new' => true,
            'carrier' => [
                'name' => 'Telkomsel',
                'badge' => 'ID',
                'color' => '#DC2626',
            ],
            'default_duration' => 30,
            'allowed_durations' => [30, 180],
            'plans' => [
                30 => [
                    'duration' => 30,
                    'label' => '30 days',
                    'price_usd' => 11.99,
                    'description' => '30-day Indonesia all-services virtual line',
                ],
                180 => [
                    'duration' => 180,
                    'label' => '180 days',
                    'price_usd' => 21.99,
                    'description' => 'Half-year 180-day Indonesia line across all services',
                ],
            ],
            'supports_per_service' => false,
            'supports_auto_renew' => true,
            'auto_renew_lockout_days' => 4,
            'supports_inventory' => false,
        ],

        'AU' => [
            'code' => 'AU',
            'aliases' => ['AUS', 'AUSTRALIA'],
            'name' => 'Australia',
            'flag' => '🇦🇺',
            'dial_code' => '+61',
            'new' => true,
            'carrier' => [
                'name' => 'Telstra',
                'badge' => 'AU',
                'color' => '#0284C7',
            ],
            'default_duration' => 30,
            'allowed_durations' => [30],
            'plans' => [
                30 => [
                    'duration' => 30,
                    'label' => '30 days',
                    'price_usd' => 22.99,
                    'description' => '30-day Australia non-VoIP all-services line',
                ],
            ],
            'supports_per_service' => false,
            'supports_auto_renew' => true,
            'auto_renew_lockout_days' => 0,
            'supports_inventory' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing and Exchange Rate Settings
    |--------------------------------------------------------------------------
    |
    | Users pay by wallet in NGN.
    | Formula: USD price x FX rate x (1 + margin), rounded up to nearest N10,
    | stored as integer kobo.
    |
    */
    'pricing' => [
        'fx_rate_usd_to_ngn' => (float) env('FX_RATE_USD_NGN', 1650.0),
        'margin_percentage' => (float) env('PROFIT_MARGIN_PCT', 15.0),
        'provider_balance_buffer_usd' => 2.0,
        'cache_duration_minutes' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | PVADeals Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'secret' => env('PVADEALS_WEBHOOK_SECRET', 'pvadeals_wh_sec_99348128340192830192849182301923'),
    ],
];
