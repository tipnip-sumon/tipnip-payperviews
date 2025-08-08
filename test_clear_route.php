<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Testing user.notifications.clear-all route ===\n";

// Check if route exists
$routeExists = Route::has('user.notifications.clear-all');
echo "Route exists: " . ($routeExists ? 'Yes' : 'No') . "\n";

if ($routeExists) {
    $route = Route::getRoutes()->getByName('user.notifications.clear-all');
    echo "Route URI: " . $route->uri() . "\n";
    echo "Route Methods: " . implode(', ', $route->methods()) . "\n";
    echo "Route Action: " . $route->getActionName() . "\n";
}

// Test URL generation
try {
    $url = route('user.notifications.clear-all');
    echo "Generated URL: " . $url . "\n";
} catch (Exception $e) {
    echo "Error generating URL: " . $e->getMessage() . "\n";
}

echo "\n=== Check notification controller method ===\n";
$controllerClass = 'App\Http\Controllers\User\NotificationController';
echo "Controller exists: " . (class_exists($controllerClass) ? 'Yes' : 'No') . "\n";

if (class_exists($controllerClass)) {
    echo "clearAll method exists: " . (method_exists($controllerClass, 'clearAll') ? 'Yes' : 'No') . "\n";
}
