<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class);

$reqUs = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=USA&currency=NGN', 'GET');
$resUs = $controller->websites($reqUs);
$dataUs = $resUs->getData(true);

echo "USA count: " . ($dataUs['total'] ?? 'error') . "\n";
echo "USA sample: " . json_encode(array_slice($dataUs['data'] ?? [], 0, 2)) . "\n";
echo "USA all_services: " . json_encode($dataUs['all_services'] ?? []) . "\n";

$reqUk = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=UK&currency=NGN', 'GET');
$resUk = $controller->websites($reqUk);
$dataUk = $resUk->getData(true);

echo "UK count: " . ($dataUk['total'] ?? 'error') . "\n";
echo "UK sample: " . json_encode(array_slice($dataUk['data'] ?? [], 0, 2)) . "\n";
echo "UK all_services: " . json_encode($dataUk['all_services'] ?? []) . "\n";

$reqHk = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=HK&currency=NGN', 'GET');
$resHk = $controller->websites($reqHk);
$dataHk = $resHk->getData(true);

echo "HK count: " . ($dataHk['total'] ?? 'error') . "\n";
echo "HK all_services: " . json_encode($dataHk['all_services'] ?? []) . "\n";
