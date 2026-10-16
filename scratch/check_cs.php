<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = \App\Models\CountryService::selectRaw('country_id, count(*) as cnt, sum(str_stock) as stock, sum(ltr_stock) as ltr')
    ->groupBy('country_id')
    ->get();

foreach ($rows as $r) {
    $c = \App\Models\Country::find($r->country_id);
    $cName = $c ? "{$c->name} ({$c->iso2})" : "Unknown ID {$r->country_id}";
    echo "Country: {$cName} -> {$r->cnt} services, {$r->stock} STR stock, {$r->ltr} LTR stock\n";
}
