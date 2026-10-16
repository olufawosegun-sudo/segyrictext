<?php
require __DIR__ . '/../vendor/autoload.php';

$key = 'API-pDmPVzCnDna6DAhdMpZGwdspOEiV1yiLgeYGiAvxua8nRJHpXq';
$baseUrl = 'https://prod-v3.pvadeals.com/v3/api';

function callApi($ep, $key) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $ep);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $key,
            'Accept: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    return json_decode($res, true) ?: $res;
}

echo "=== INVENTORY STR ===\n";
$invStr = callApi('/inventory?type=STR', $key);
echo json_encode($invStr, JSON_PRETTY_PRINT) . "\n\n";

echo "=== INVENTORY LTR ===\n";
$invLtr = callApi('/inventory?type=LTR', $key);
echo json_encode($invLtr, JSON_PRETTY_PRINT) . "\n\n";
