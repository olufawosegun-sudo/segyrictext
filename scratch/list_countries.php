<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$cs = \App\Models\Country::where('is_active', true)->get();
foreach ($cs as $c) {
    echo $c->id . ': ' . $c->name . ' (' . $c->iso2 . '/' . $c->iso3 . ")\n";
}
