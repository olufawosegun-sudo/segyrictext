<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sync = app(\App\Services\PvaDeals\PvaDealsSyncService::class);
$res = $sync->sync();
echo "Sync result: " . json_encode($res) . "\n";
