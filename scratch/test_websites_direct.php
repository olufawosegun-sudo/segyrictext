<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$req = \Illuminate\Http\Request::create('/api/v1/pvadeals/websites?country=USA&currency=NGN', 'GET');
$controller = app(\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class);
$res = $controller->websites($req);
$data = $res->getData(true);

echo "Status: " . $res->getStatusCode() . "\n";
echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
echo "Total items: " . ($data['total'] ?? 0) . "\n";
echo "First 3 items:\n";
foreach (array_slice($data['data'] ?? [], 0, 3) as $item) {
    echo " - ID: {$item['id']}, Name: {$item['name']}, Price: {$item['price']}, Stock: {$item['stock']}, LTR3: {$item['ltr3']}, LTR7: {$item['ltr7']}, LTR14: {$item['ltr14']}, LTR30: {$item['ltr30']}\n";
}
