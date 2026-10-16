<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\PricingRule;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ComprehensiveCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Currencies
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Ghanaian Cedi', 'code' => 'GHS', 'symbol' => 'GH₵', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'symbol' => 'R', 'decimal_places' => 2, 'is_active' => true],
            ['name' => 'Ugandan Shilling', 'code' => 'UGX', 'symbol' => 'USh', 'decimal_places' => 0, 'is_active' => true],
            ['name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => 'E£', 'decimal_places' => 2, 'is_active' => true],
        ];

        $currencyMap = [];
        foreach ($currencies as $c) {
            $currencyMap[$c['code']] = Currency::updateOrCreate(['code' => $c['code']], $c);
        }

        // 2. Exchange Rates against USD (base: USD)
        $usd = $currencyMap['USD'];
        $rates = [
            'USD' => 1.0,
            'NGN' => 1650.0,
            'KES' => 130.0,
            'GHS' => 15.5,
            'ZAR' => 18.2,
            'UGX' => 3700.0,
            'EGP' => 48.5,
            'EUR' => 0.92,
            'GBP' => 0.78,
            'CAD' => 1.36,
        ];

        foreach ($rates as $quoteCode => $rate) {
            if (isset($currencyMap[$quoteCode])) {
                ExchangeRate::updateOrCreate(
                    [
                        'base_currency_id' => $usd->id,
                        'quote_currency_id' => $currencyMap[$quoteCode]->id,
                    ],
                    [
                        'rate' => $rate,
                        'source' => 'system_default',
                        'effective_at' => now(),
                    ]
                );
            }
        }

        // 3. Countries
        $countries = [
            ['name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD'],
            ['name' => 'United Kingdom', 'iso2' => 'GB', 'iso3' => 'GBR', 'dial_code' => '+44', 'currency_code' => 'GBP'],
            ['name' => 'Canada', 'iso2' => 'CA', 'iso3' => 'CAN', 'dial_code' => '+1', 'currency_code' => 'CAD'],
            ['name' => 'Germany', 'iso2' => 'DE', 'iso3' => 'DEU', 'dial_code' => '+49', 'currency_code' => 'EUR'],
            ['name' => 'France', 'iso2' => 'FR', 'iso3' => 'FRA', 'dial_code' => '+33', 'currency_code' => 'EUR'],
            ['name' => 'Netherlands', 'iso2' => 'NL', 'iso3' => 'NLD', 'dial_code' => '+31', 'currency_code' => 'EUR'],
            ['name' => 'Spain', 'iso2' => 'ES', 'iso3' => 'ESP', 'dial_code' => '+34', 'currency_code' => 'EUR'],
            ['name' => 'Italy', 'iso2' => 'IT', 'iso3' => 'ITA', 'dial_code' => '+39', 'currency_code' => 'EUR'],
            ['name' => 'Australia', 'iso2' => 'AU', 'iso3' => 'AUS', 'dial_code' => '+61', 'currency_code' => 'AUD'],
            ['name' => 'Brazil', 'iso2' => 'BR', 'iso3' => 'BRA', 'dial_code' => '+55', 'currency_code' => 'BRL'],
            ['name' => 'India', 'iso2' => 'IN', 'iso3' => 'IND', 'dial_code' => '+91', 'currency_code' => 'INR'],
            ['name' => 'Nigeria', 'iso2' => 'NG', 'iso3' => 'NGA', 'dial_code' => '+234', 'currency_code' => 'NGN'],
            ['name' => 'Ghana', 'iso2' => 'GH', 'iso3' => 'GHA', 'dial_code' => '+233', 'currency_code' => 'GHS'],
            ['name' => 'Kenya', 'iso2' => 'KE', 'iso3' => 'KEN', 'dial_code' => '+254', 'currency_code' => 'KES'],
            ['name' => 'South Africa', 'iso2' => 'ZA', 'iso3' => 'ZAF', 'dial_code' => '+27', 'currency_code' => 'ZAR'],
            ['name' => 'Philippines', 'iso2' => 'PH', 'iso3' => 'PHL', 'dial_code' => '+63', 'currency_code' => 'PHP'],
            ['name' => 'Indonesia', 'iso2' => 'ID', 'iso3' => 'IDN', 'dial_code' => '+62', 'currency_code' => 'IDR'],
            ['name' => 'Poland', 'iso2' => 'PL', 'iso3' => 'POL', 'dial_code' => '+48', 'currency_code' => 'PLN'],
            ['name' => 'Sweden', 'iso2' => 'SE', 'iso3' => 'SWE', 'dial_code' => '+46', 'currency_code' => 'SEK'],
            ['name' => 'Turkey', 'iso2' => 'TR', 'iso3' => 'TUR', 'dial_code' => '+90', 'currency_code' => 'TRY'],
            ['name' => 'Mexico', 'iso2' => 'MX', 'iso3' => 'MEX', 'dial_code' => '+52', 'currency_code' => 'MXN'],
            ['name' => 'Argentina', 'iso2' => 'AR', 'iso3' => 'ARG', 'dial_code' => '+54', 'currency_code' => 'ARS'],
            ['name' => 'Colombia', 'iso2' => 'CO', 'iso3' => 'COL', 'dial_code' => '+57', 'currency_code' => 'COP'],
            ['name' => 'Vietnam', 'iso2' => 'VN', 'iso3' => 'VNM', 'dial_code' => '+84', 'currency_code' => 'VND'],
            ['name' => 'Thailand', 'iso2' => 'TH', 'iso3' => 'THA', 'dial_code' => '+66', 'currency_code' => 'THB'],
            ['name' => 'Egypt', 'iso2' => 'EG', 'iso3' => 'EGY', 'dial_code' => '+20', 'currency_code' => 'EGP'],
            ['name' => 'United Arab Emirates', 'iso2' => 'AE', 'iso3' => 'ARE', 'dial_code' => '+971', 'currency_code' => 'AED'],
            ['name' => 'Israel', 'iso2' => 'IL', 'iso3' => 'ISR', 'dial_code' => '+972', 'currency_code' => 'ILS'],
            ['name' => 'Portugal', 'iso2' => 'PT', 'iso3' => 'PRT', 'dial_code' => '+351', 'currency_code' => 'EUR'],
            ['name' => 'Ireland', 'iso2' => 'IE', 'iso3' => 'IRL', 'dial_code' => '+353', 'currency_code' => 'EUR'],
            ['name' => 'Hong Kong', 'iso2' => 'HK', 'iso3' => 'HKG', 'dial_code' => '+852', 'currency_code' => 'HKD'],
            ['name' => 'Singapore', 'iso2' => 'SG', 'iso3' => 'SGP', 'dial_code' => '+65', 'currency_code' => 'SGD'],
            ['name' => 'Malaysia', 'iso2' => 'MY', 'iso3' => 'MYS', 'dial_code' => '+60', 'currency_code' => 'MYR'],
            ['name' => 'Japan', 'iso2' => 'JP', 'iso3' => 'JPN', 'dial_code' => '+81', 'currency_code' => 'JPY'],
            ['name' => 'South Korea', 'iso2' => 'KR', 'iso3' => 'KOR', 'dial_code' => '+82', 'currency_code' => 'KRW'],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(['iso2' => $c['iso2']], array_merge($c, ['is_active' => true]));
        }

        // 4. Categories
        $catSocial = ServiceCategory::firstOrCreate(['name' => 'Social & Messaging'], ['description' => 'Social networks, chat apps and messaging']);
        $catFintech = ServiceCategory::firstOrCreate(['name' => 'Finance & Crypto'], ['description' => 'Wallets, payment gateways and cryptocurrency']);
        $catAI = ServiceCategory::firstOrCreate(['name' => 'AI & Technology'], ['description' => 'Artificial intelligence, dev tools and tech giants']);
        $catShopping = ServiceCategory::firstOrCreate(['name' => 'Shopping & Delivery'], ['description' => 'E-commerce, marketplaces and food delivery']);
        $catDating = ServiceCategory::firstOrCreate(['name' => 'Dating & Entertainment'], ['description' => 'Dating platforms, streaming and games']);
        $catUniversal = ServiceCategory::firstOrCreate(['name' => 'Universal / Other'], ['description' => 'Universal verifications and custom platforms']);

        // 5. Comprehensive Services
        $services = [
            // Social & Messaging
            ['name' => 'WhatsApp', 'code' => 'whatsapp', 'description' => 'Meta WhatsApp messenger activation', 'cat' => $catSocial],
            ['name' => 'Telegram', 'code' => 'telegram', 'description' => 'Telegram cloud messaging verification', 'cat' => $catSocial],
            ['name' => 'Facebook', 'code' => 'facebook', 'description' => 'Facebook & Meta family of apps', 'cat' => $catSocial],
            ['name' => 'Instagram', 'code' => 'instagram', 'description' => 'Instagram account verification', 'cat' => $catSocial],
            ['name' => 'Twitter / X', 'code' => 'twitter', 'description' => 'X (formerly Twitter) phone confirmation', 'cat' => $catSocial],
            ['name' => 'TikTok', 'code' => 'tiktok', 'description' => 'TikTok creator & consumer account', 'cat' => $catSocial],
            ['name' => 'Discord', 'code' => 'discord', 'description' => 'Discord server & user phone verification', 'cat' => $catSocial],
            ['name' => 'Snapchat', 'code' => 'snapchat', 'description' => 'Snapchat account activation', 'cat' => $catSocial],
            ['name' => 'LinkedIn', 'code' => 'linkedin', 'description' => 'LinkedIn professional profile verification', 'cat' => $catSocial],
            ['name' => 'Reddit', 'code' => 'reddit', 'description' => 'Reddit user verification', 'cat' => $catSocial],
            ['name' => 'Viber', 'code' => 'viber', 'description' => 'Rakuten Viber messaging confirmation', 'cat' => $catSocial],
            ['name' => 'WeChat', 'code' => 'wechat', 'description' => 'Tencent WeChat mobile verification', 'cat' => $catSocial],
            ['name' => 'LINE', 'code' => 'line', 'description' => 'LINE instant communications', 'cat' => $catSocial],
            ['name' => 'KakaoTalk', 'code' => 'kakaotalk', 'description' => 'KakaoTalk verification', 'cat' => $catSocial],
            ['name' => 'Signal', 'code' => 'signal', 'description' => 'Signal private messenger verification', 'cat' => $catSocial],

            // AI & Big Tech
            ['name' => 'Google / Gmail', 'code' => 'google', 'description' => 'Google Workspace, Gmail & YouTube', 'cat' => $catAI],
            ['name' => 'OpenAI / ChatGPT', 'code' => 'openai', 'description' => 'ChatGPT & OpenAI developer API activation', 'cat' => $catAI],
            ['name' => 'Claude / Anthropic', 'code' => 'claude', 'description' => 'Anthropic Claude AI account', 'cat' => $catAI],
            ['name' => 'Microsoft', 'code' => 'microsoft', 'description' => 'Microsoft 365, Outlook, Azure & Xbox', 'cat' => $catAI],
            ['name' => 'Apple ID / iCloud', 'code' => 'apple', 'description' => 'Apple ID & iOS App Store activation', 'cat' => $catAI],
            ['name' => 'GitHub', 'code' => 'github', 'description' => 'GitHub developer 2FA confirmation', 'cat' => $catAI],
            ['name' => 'Yahoo', 'code' => 'yahoo', 'description' => 'Yahoo Mail verification', 'cat' => $catAI],

            // Finance & Crypto
            ['name' => 'PayPal', 'code' => 'paypal', 'description' => 'PayPal account & payment verification', 'cat' => $catFintech],
            ['name' => 'Stripe', 'code' => 'stripe', 'description' => 'Stripe merchant verification', 'cat' => $catFintech],
            ['name' => 'Wise', 'code' => 'wise', 'description' => 'Wise multi-currency account', 'cat' => $catFintech],
            ['name' => 'Binance', 'code' => 'binance', 'description' => 'Binance crypto exchange verification', 'cat' => $catFintech],
            ['name' => 'Coinbase', 'code' => 'coinbase', 'description' => 'Coinbase cryptocurrency exchange', 'cat' => $catFintech],
            ['name' => 'Cash App', 'code' => 'cashapp', 'description' => 'Block Cash App peer-to-peer payments', 'cat' => $catFintech],
            ['name' => 'Venmo', 'code' => 'venmo', 'description' => 'Venmo mobile payments', 'cat' => $catFintech],
            ['name' => 'Revolut', 'code' => 'revolut', 'description' => 'Revolut digital banking verification', 'cat' => $catFintech],
            ['name' => 'Skrill', 'code' => 'skrill', 'description' => 'Skrill digital wallet', 'cat' => $catFintech],
            ['name' => 'Flutterwave', 'code' => 'flutterwave', 'description' => 'Flutterwave payments', 'cat' => $catFintech],

            // Shopping, Delivery & Gig
            ['name' => 'Amazon', 'code' => 'amazon', 'description' => 'Amazon shopping & Prime verification', 'cat' => $catShopping],
            ['name' => 'eBay', 'code' => 'ebay', 'description' => 'eBay marketplace phone confirmation', 'cat' => $catShopping],
            ['name' => 'Uber / Uber Eats', 'code' => 'uber', 'description' => 'Uber rider and driver activation', 'cat' => $catShopping],
            ['name' => 'Bolt', 'code' => 'bolt', 'description' => 'Bolt rides and delivery', 'cat' => $catShopping],
            ['name' => 'DoorDash', 'code' => 'doordash', 'description' => 'DoorDash food delivery', 'cat' => $catShopping],
            ['name' => 'Airbnb', 'code' => 'airbnb', 'description' => 'Airbnb guest & host verification', 'cat' => $catShopping],
            ['name' => 'Upwork', 'code' => 'upwork', 'description' => 'Upwork freelance platform activation', 'cat' => $catShopping],
            ['name' => 'Fiverr', 'code' => 'fiverr', 'description' => 'Fiverr freelancer verification', 'cat' => $catShopping],

            // Dating & Gaming
            ['name' => 'Tinder', 'code' => 'tinder', 'description' => 'Tinder dating app verification', 'cat' => $catDating],
            ['name' => 'Bumble', 'code' => 'bumble', 'description' => 'Bumble dating and networking', 'cat' => $catDating],
            ['name' => 'Badoo', 'code' => 'badoo', 'description' => 'Badoo social discovery', 'cat' => $catDating],
            ['name' => 'Steam', 'code' => 'steam', 'description' => 'Valve Steam gaming security', 'cat' => $catDating],
            ['name' => 'Netflix', 'code' => 'netflix', 'description' => 'Netflix streaming account setup', 'cat' => $catDating],
            ['name' => 'Spotify', 'code' => 'spotify', 'description' => 'Spotify music streaming verification', 'cat' => $catDating],
            ['name' => 'Blizzard / Battle.net', 'code' => 'blizzard', 'description' => 'Battle.net gamer verification', 'cat' => $catDating],

            // Universal
            ['name' => 'Any Other Service', 'code' => 'other', 'description' => 'Universal line for any app or website verification', 'cat' => $catUniversal],
        ];

        foreach ($services as $s) {
            $cat = $s['cat'];
            unset($s['cat']);

            $service = Service::updateOrCreate(
                ['code' => $s['code']],
                array_merge($s, ['is_active' => true])
            );

            if ($cat) {
                $cat->services()->syncWithoutDetaching([$service->id]);
            }
        }
    }
}
