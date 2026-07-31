<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Mock a login request
$request = Request::create('/api/login', 'POST', [
    'email' => 'admin@admin.com',
    'password' => 'password',
    'cf-turnstile-response' => 'dummy-token'
]);

try {
    $controller = app(AuthController::class);
    $response = $controller->login($request);
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "Error Type: " . get_class($e) . "\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
