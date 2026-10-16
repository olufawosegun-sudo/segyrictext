<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

try {
    $controller = app(\App\Http\Controllers\Mobile\MobileAppController::class);
    $response = $controller->home();
    $html = is_string($response) ? $response : (method_exists($response, 'getContent') ? $response->getContent() : (string) $response);
    echo "Rendered successfully! Length: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "Error rendering: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
