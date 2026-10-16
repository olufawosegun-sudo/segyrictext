<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$client = new \App\Services\PvaDeals\PvaDealsClient();
echo "Health: " . json_encode($client->checkHealth()) . "\n";

// Let's test fetch services directly
$res = $client->getAllServices(true);
echo "Fetched all services: " . count($res) . "\n";
if (count($res) > 0) {
    $ukServices = array_filter($res, fn($s) => ($s['country'] ?? '') === 'UK');
    echo "UK services in provider response: " . count($ukServices) . "\n";
    $usServices = array_filter($res, fn($s) => ($s['country'] ?? '') === 'US');
    echo "US services in provider response: " . count($usServices) . "\n";
}
