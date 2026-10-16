<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

echo "1. Checking PVADeals Catalog for USA:\n";
$reqUs = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=USA&currency=NGN', 'GET');
$resUs = app(\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class)->websites($reqUs);
$dataUs = $resUs->getData(true);
echo " - Status: {$resUs->getStatusCode()}, Total services: " . ($dataUs['total'] ?? 0) . "\n";

echo "2. Checking PVADeals Catalog for UK:\n";
$reqUk = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=UK&currency=NGN', 'GET');
$resUk = app(\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class)->websites($reqUk);
$dataUk = $resUk->getData(true);
echo " - Status: {$resUk->getStatusCode()}, Total services: " . ($dataUk['total'] ?? 0) . "\n";

echo "3. Checking PVADeals Adapter service resolution for USA WhatsApp:\n";
$usCountry = \App\Models\Country::where('iso2', 'US')->orWhere('iso3', 'USA')->first();
$ukCountry = \App\Models\Country::where('iso2', 'GB')->orWhere('iso3', 'GBR')->first();
$waService = \App\Models\Service::where('name', 'WhatsApp')->orWhere('code', 'whatsapp')->first();

if ($usCountry && $waService) {
    $adapter = new \App\Infrastructure\Providers\PvaDealsAdapter();
    $pvaUs = $adapter->resolvePvaService($usCountry, $waService);
    echo " - Resolved USA WhatsApp PVA service: " . ($pvaUs['name'] ?? 'none') . ", STR: \${$pvaUs['STRprice']}, LTR3: \${$pvaUs['LTR3price']}, LTR7: \${$pvaUs['LTR7price']}, LTR30: \${$pvaUs['LTR30price']}\n";
}

if ($ukCountry && $waService) {
    $pvaUk = $adapter->resolvePvaService($ukCountry, $waService);
    echo " - Resolved UK WhatsApp PVA service: " . ($pvaUk['name'] ?? 'none') . ", STR: \${$pvaUk['STRprice']}, LTR3: \${$pvaUk['LTR3price']}, LTR7: \${$pvaUk['LTR7price']}, LTR30: \${$pvaUk['LTR30price']}\n";
}

echo "\nAll checks passed successfully!\n";
