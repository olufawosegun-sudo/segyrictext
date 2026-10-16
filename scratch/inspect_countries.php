<?php
require __DIR__ . '/../vendor/autoload.php';

$key = 'API-pDmPVzCnDna6DAhdMpZGwdspOEiV1yiLgeYGiAvxua8nRJHpXq';
$baseUrl = 'https://prod-v3.pvadeals.com/v3/api';

$ch = curl_init($baseUrl . '/services/all');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $key,
        'Accept: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
]);
$res = curl_exec($ch);
$json = json_decode($res, true);

$services = $json['data']['services'] ?? $json['data'] ?? [];
$countries = [];
$totalStrStock = 0;
$totalLtrStock = 0;
foreach ($services as $s) {
    $c = $s['country'] ?? 'unknown';
    if (!isset($countries[$c])) {
        $countries[$c] = ['count' => 0, 'strStock' => 0, 'ltrStock' => 0, 'sample' => $s['name']];
    }
    $countries[$c]['count']++;
    $countries[$c]['strStock'] += ($s['STRstock'] ?? 0);
    $countries[$c]['ltrStock'] += ($s['LTRstock'] ?? 0);
    $totalStrStock += ($s['STRstock'] ?? 0);
    $totalLtrStock += ($s['LTRstock'] ?? 0);
}

echo "Total services: " . count($services) . "\n";
echo "Total STR stock: $totalStrStock, Total LTR stock: $totalLtrStock\n";
echo "Countries in /services/all:\n";
print_r($countries);
