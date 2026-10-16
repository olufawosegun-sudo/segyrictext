<?php
require __DIR__ . '/../vendor/autoload.php';

$key = 'API-pDmPVzCnDna6DAhdMpZGwdspOEiV1yiLgeYGiAvxua8nRJHpXq';
$baseUrl = 'https://prod-v3.pvadeals.com/v3/api';

$ch = curl_init($baseUrl . '/services/all?limit=20');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$res = curl_exec($ch);
$json = json_decode($res, true);

echo "Total: " . ($json['data']['pagination']['totalCount'] ?? 'N/A') . "\n";
$services = $json['data']['services'] ?? [];
$countries = [];
foreach ($services as $s) {
    $countries[$s['country']] = ($countries[$s['country']] ?? 0) + 1;
}
echo "Sample countries in first 20: " . json_encode($countries) . "\n";
echo "First 2 services:\n" . json_encode(array_slice($services, 0, 2), JSON_PRETTY_PRINT) . "\n";
