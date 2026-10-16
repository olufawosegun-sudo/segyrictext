<?php
require __DIR__ . '/../vendor/autoload.php';

$key = 'API-pDmPVzCnDna6DAhdMpZGwdspOEiV1yiLgeYGiAvxua8nRJHpXq';
$baseUrl = 'https://prod-v3.pvadeals.com/v3/api';

function testEndpoint($url, $key, $method = 'GET', $body = null) {
    $ch = curl_init($url);
    $headers = [
        'Authorization: Bearer ' . $key,
        'Accept: application/json',
    ];
    if ($body) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15,
    ]);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    return ['code' => $httpCode, 'response' => $res, 'error' => $err];
}

$endpoints = [
    '/health',
    '/balance',
    '/countries',
    '/services/all',
    '/services',
    '/numbers',
    '/rentals',
    '/inventory',
    '/pricing',
    '/requests',
];

foreach ($endpoints as $ep) {
    $res = testEndpoint($baseUrl . $ep, $key);
    echo "=== Endpoint: $ep (HTTP {$res['code']}) ===\n";
    $json = json_decode($res['response'], true);
    if ($json) {
        if (isset($json['data']) && is_array($json['data'])) {
            $count = count($json['data']);
            echo "Data keys/count: $count items. Sample: " . json_encode(array_slice($json['data'], 0, 2)) . "\n";
        } else {
            echo "Response: " . substr($res['response'], 0, 300) . "\n";
        }
    } else {
        echo "Raw: " . substr($res['response'], 0, 200) . "\n";
    }
}
