<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (\App\Models\ProviderAccount::all() as $a) {
    echo "Account: {$a->name} (provider_id: {$a->provider_id})\n";
    echo "Credentials: " . json_encode($a->credentials) . "\n";
    echo "Status: {$a->status}, Balance: {$a->balance}\n";
}
