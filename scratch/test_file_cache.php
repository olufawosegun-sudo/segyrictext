<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$cached = \Illuminate\Support\Facades\Cache::store('file')->get('test_key');
\Illuminate\Support\Facades\Cache::store('file')->put('test_key', ['hello' => 'world'], 60);
echo "File cache works: " . json_encode(\Illuminate\Support\Facades\Cache::store('file')->get('test_key')) . "\n";
