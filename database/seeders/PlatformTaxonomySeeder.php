<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryRegion;
use App\Models\CountryService;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\Permission;
use App\Models\PricingRule;
use App\Models\Provider;
use App\Models\Region;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Full administrative sovereignty'],
            ['name' => 'Support Agent', 'description' => 'Customer assistance & session inspection'],
            ['name' => 'Financial Auditor', 'description' => 'Ledger, payouts, refunds reconciliation'],
            ['name' => 'Customer', 'description' => 'Standard consumer verification user'],
            ['name' => 'Developer', 'description' => 'API consumer with key provisioning'],
        ];

        $roleModels = [];
        foreach ($roles as $r) {
            $roleModels[$r['name']] = Role::firstOrCreate(['name' => $r['name']], $r);
        }

        $permissions = [
            ['name' => 'Manage Numbers', 'description' => 'Direct inventory and carrier routing control'],
            ['name' => 'View Inventory', 'description' => 'Inspect numbers, providers, and rate cards'],
            ['name' => 'Execute Orders', 'description' => 'Place verification and rental orders'],
            ['name' => 'Process Refunds', 'description' => 'Handle order cancellation and wallet credit'],
            ['name' => 'Adjust Wallets', 'description' => 'Administrative ledger correction and grants'],
            ['name' => 'Configure Providers', 'description' => 'Provider credentials and webhook routing'],
            ['name' => 'Access Developer API', 'description' => 'Generate and use programmatic API keys'],
            ['name' => 'Audit System Logs', 'description' => 'View immutable security and activity logs'],
        ];

        foreach ($permissions as $p) {
            $perm = Permission::firstOrCreate(['name' => $p['name']], $p);
            $roleModels['Super Admin']->permissions()->syncWithoutDetaching([$perm->id]);
        }

        // 2. Currencies
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true],
        ];

        $currencyModels = [];
        foreach ($currencies as $c) {
            $currencyModels[$c['code']] = Currency::firstOrCreate(['code' => $c['code']], $c);
        }

        // 3. Regions
        $regions = [
            ['name' => 'North America', 'code' => 'NA'],
            ['name' => 'Europe', 'code' => 'EU'],
            ['name' => 'West Africa', 'code' => 'WA'],
            ['name' => 'South Asia', 'code' => 'SA'],
            ['name' => 'South America', 'code' => 'SAM'],
        ];

        $regionModels = [];
        foreach ($regions as $reg) {
            $regionModels[$reg['code']] = Region::firstOrCreate(['code' => $reg['code']], $reg);
        }

        // 4. Number Types
        $numberTypes = [
            ['name' => 'Mobile Cellular', 'code' => 'mobile', 'description' => 'Carrier SIM numbers with high verification success rate.'],
            ['name' => 'Virtual VoIP', 'code' => 'virtual', 'description' => 'Cloud hosted telephony numbers.'],
            ['name' => 'Toll-Free', 'code' => 'toll_free', 'description' => 'Enterprise 800/888 inbound numbers.'],
            ['name' => 'Landline', 'code' => 'landline', 'description' => 'Fixed-line PSTN terminals.'],
        ];

        $typeModels = [];
        foreach ($numberTypes as $nt) {
            $typeModels[$nt['code']] = NumberType::firstOrCreate(['code' => $nt['code']], $nt);
        }

        // 5. Countries
        $countries = [
            ['name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD', 'region' => 'NA'],
            ['name' => 'United Kingdom', 'iso2' => 'GB', 'iso3' => 'GBR', 'dial_code' => '+44', 'currency_code' => 'GBP', 'region' => 'EU'],
            ['name' => 'Canada', 'iso2' => 'CA', 'iso3' => 'CAN', 'dial_code' => '+1', 'currency_code' => 'CAD', 'region' => 'NA'],
            ['name' => 'Germany', 'iso2' => 'DE', 'iso3' => 'DEU', 'dial_code' => '+49', 'currency_code' => 'EUR', 'region' => 'EU'],
            ['name' => 'Nigeria', 'iso2' => 'NG', 'iso3' => 'NGA', 'dial_code' => '+234', 'currency_code' => 'NGN', 'region' => 'WA'],
            ['name' => 'France', 'iso2' => 'FR', 'iso3' => 'FRA', 'dial_code' => '+33', 'currency_code' => 'EUR', 'region' => 'EU'],
            ['name' => 'Netherlands', 'iso2' => 'NL', 'iso3' => 'NLD', 'dial_code' => '+31', 'currency_code' => 'EUR', 'region' => 'EU'],
            ['name' => 'India', 'iso2' => 'IN', 'iso3' => 'IND', 'dial_code' => '+91', 'currency_code' => 'INR', 'region' => 'SA'],
            ['name' => 'Brazil', 'iso2' => 'BR', 'iso3' => 'BRA', 'dial_code' => '+55', 'currency_code' => 'BRL', 'region' => 'SAM'],
            ['name' => 'Ghana', 'iso2' => 'GH', 'iso3' => 'GHA', 'dial_code' => '+233', 'currency_code' => 'GHS', 'region' => 'WA'],
        ];

        $countryModels = [];
        foreach ($countries as $c) {
            $regCode = $c['region'];
            unset($c['region']);
            $country = Country::firstOrCreate(['iso2' => $c['iso2']], $c);
            $countryModels[$c['iso2']] = $country;

            if (isset($regionModels[$regCode])) {
                CountryRegion::firstOrCreate([
                    'country_id' => $country->id,
                    'region_id' => $regionModels[$regCode]->id,
                ]);
            }
        }

        // 6. Service Categories & Services
        $categories = [
            'Messaging' => 'Instant messaging and secure chat applications',
            'Social' => 'Social networks, microblogging, and creator platforms',
            'Tech & Cloud' => 'Cloud services, email providers, and developer infrastructure',
            'AI & Automation' => 'Artificial intelligence models and bot services',
            'Gaming' => 'Gaming platforms, launchers, and anti-fraud systems',
            'Transit & On-Demand' => 'Ridershare, deliveries, and local service utilities',
        ];

        $categoryModels = [];
        foreach ($categories as $catName => $catDesc) {
            $categoryModels[$catName] = ServiceCategory::firstOrCreate(
                ['name' => $catName],
                ['name' => $catName, 'description' => $catDesc, 'is_active' => true]
            );
        }

        $services = [
            ['name' => 'WhatsApp', 'code' => 'whatsapp', 'category' => 'Messaging', 'description' => 'Instant phone number registration for WhatsApp messenger.'],
            ['name' => 'Telegram', 'code' => 'telegram', 'category' => 'Messaging', 'description' => 'Secure SMS authorization codes for Telegram.'],
            ['name' => 'Google / Gmail', 'code' => 'google', 'category' => 'Tech & Cloud', 'description' => 'Account creation and multi-factor 2FA verification codes.'],
            ['name' => 'OpenAI / ChatGPT', 'code' => 'openai', 'category' => 'AI & Automation', 'description' => 'AI platform developer and ChatGPT consumer account registration.'],
            ['name' => 'Microsoft', 'code' => 'microsoft', 'category' => 'Tech & Cloud', 'description' => 'Outlook, Azure, and Microsoft 365 identity confirmation.'],
            ['name' => 'Facebook', 'code' => 'facebook', 'category' => 'Social', 'description' => 'Meta Facebook account verification and password reset.'],
            ['name' => 'Instagram', 'code' => 'instagram', 'category' => 'Social', 'description' => 'Instagram profiles, creators, and business handles.'],
            ['name' => 'Twitter / X', 'code' => 'twitter-x', 'category' => 'Social', 'description' => 'X (formerly Twitter) microblogging registration.'],
            ['name' => 'TikTok', 'code' => 'tiktok', 'category' => 'Social', 'description' => 'Short-form media creator phone authorization.'],
            ['name' => 'Discord', 'code' => 'discord', 'category' => 'Gaming', 'description' => 'Community server phone verification anti-raid filter.'],
            ['name' => 'Steam', 'code' => 'steam', 'category' => 'Gaming', 'description' => 'Valve Steam Guard mobile authentication.'],
            ['name' => 'Uber', 'code' => 'uber', 'category' => 'Transit & On-Demand', 'description' => 'Rider and driver transit SMS dispatch.'],
        ];

        $serviceModels = [];
        foreach ($services as $s) {
            $catName = $s['category'];
            unset($s['category']);
            $service = Service::firstOrCreate(['code' => $s['code']], $s);
            $serviceModels[$s['code']] = $service;

            if (isset($categoryModels[$catName])) {
                $categoryModels[$catName]->services()->syncWithoutDetaching([$service->id]);
            }
        }

        // 7. Aggregate Telecom Providers
        $providers = [
            ['name' => 'Twilio Telecom Carrier', 'code' => 'twilio', 'type' => 'aggregator', 'status' => 'active', 'priority' => 1],
            ['name' => 'PVADeals v3 API Gateway', 'code' => 'pvadeals', 'type' => 'aggregator', 'status' => 'active', 'priority' => 2],
            ['name' => '5SIM Direct Gateway', 'code' => '5sim', 'type' => 'sim_bank', 'status' => 'active', 'priority' => 3],
            ['name' => 'SMS-Activate Global', 'code' => 'sms-activate', 'type' => 'aggregator', 'status' => 'active', 'priority' => 4],
        ];

        $providerModels = [];
        foreach ($providers as $p) {
            $providerModels[$p['code']] = Provider::firstOrCreate(['code' => $p['code']], $p);
        }

        // 8. Populate Country-Service and Pricing Rules
        $basePrices = [
            'whatsapp' => ['cost' => 0.40, 'sell' => 0.85],
            'telegram' => ['cost' => 0.35, 'sell' => 0.75],
            'google' => ['cost' => 0.45, 'sell' => 0.90],
            'openai' => ['cost' => 0.60, 'sell' => 1.20],
            'microsoft' => ['cost' => 0.35, 'sell' => 0.70],
            'facebook' => ['cost' => 0.30, 'sell' => 0.65],
            'instagram' => ['cost' => 0.30, 'sell' => 0.65],
            'twitter-x' => ['cost' => 0.40, 'sell' => 0.80],
            'tiktok' => ['cost' => 0.30, 'sell' => 0.60],
            'discord' => ['cost' => 0.25, 'sell' => 0.50],
            'steam' => ['cost' => 0.25, 'sell' => 0.55],
            'uber' => ['cost' => 0.35, 'sell' => 0.75],
        ];

        $mobileType = $typeModels['mobile'] ?? null;
        $primaryProvider = $providerModels['twilio'] ?? null;

        foreach ($countryModels as $iso2 => $country) {
            foreach ($serviceModels as $code => $service) {
                CountryService::updateOrCreate(
                    ['country_id' => $country->id, 'service_id' => $service->id],
                    ['is_active' => true]
                );

                $pricing = $basePrices[$code] ?? ['cost' => 0.35, 'sell' => 0.80];

                PricingRule::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'service_id' => $service->id,
                        'number_type_id' => $mobileType?->id,
                    ],
                    [
                        'provider_id' => $primaryProvider?->id,
                        'purchase_price' => $pricing['cost'],
                        'selling_price' => $pricing['sell'],
                        'currency_code' => 'USD',
                        'priority' => 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 9. System Settings
        $settings = [
            ['key' => 'platform_name', 'value' => 'Verixa Virtual Number & Verification Platform', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'group' => 'financial', 'is_public' => true],
            ['key' => 'min_wallet_deposit', 'value' => '5.00', 'type' => 'decimal', 'group' => 'financial', 'is_public' => true],
            ['key' => 'session_timeout_seconds', 'value' => '1200', 'type' => 'integer', 'group' => 'verification', 'is_public' => false],
            ['key' => 'max_verification_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'verification', 'is_public' => false],
            ['key' => 'auto_refund_expired_sessions', 'value' => 'true', 'type' => 'boolean', 'group' => 'verification', 'is_public' => false],
        ];

        foreach ($settings as $set) {
            Setting::updateOrCreate(['key' => $set['key']], $set);
        }

        // 10. Admin User & Double-Entry Initial Wallet
        $admin = User::firstOrCreate(
            ['email' => 'admin@verixa.io'],
            [
                'name' => 'Alex Morgan',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin->roles()->syncWithoutDetaching([$roleModels['Super Admin']->id]);

        $usdCurrency = $currencyModels['USD'];

        $adminWallet = Wallet::firstOrCreate(
            ['user_id' => $admin->id, 'currency_id' => $usdCurrency->id],
            [
                'balance' => 250.0000,
                'status' => 'active',
            ]
        );

        if ($adminWallet->wasRecentlyCreated) {
            WalletTransaction::create([
                'wallet_id' => $adminWallet->id,
                'user_id' => $admin->id,
                'type' => 'deposit',
                'amount' => 250.0000,
                'balance_before' => 0.0000,
                'balance_after' => 250.0000,
                'reference' => 'TXN_' . \Illuminate\Support\Str::ulid(),
                'source_type' => 'initial_grant',
                'source_id' => 1,
                'description' => 'Initial operational platform funding balance',
                'created_at' => now(),
            ]);
        }
    }
}
