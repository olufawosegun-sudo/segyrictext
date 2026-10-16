<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$uk = \App\Models\Country::whereIn('iso2', ['GB', 'UK'])->get();
echo "UK Country rows: " . json_encode($uk) . "\n";

$catService = app(\App\Services\PvaDeals\PvaDealsCatalogService::class);
$websitesUk = $catService->getWebsites('UK');
echo "UK websites count: " . $websitesUk['total'] . "\n";
echo "First 2 websites: " . json_encode(array_slice($websitesUk['services'], 0, 2)) . "\n";

$websitesUs = $catService->getWebsites('USA');
echo "USA websites count: " . $websitesUs['total'] . "\n";

$websitesHk = $catService->getWebsites('HK');
echo "HK websites count: " . $websitesHk['total'] . "\n";
