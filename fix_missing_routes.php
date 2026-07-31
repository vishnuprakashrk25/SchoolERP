<?php
$file = __DIR__ . '/routes/api.php';
$content = file_get_contents($file);

// Find all controllers
preg_match_all('/([A-Za-z0-9_]+Controller)::class/', $content, $matches);
$controllers = array_unique($matches[1]);

foreach ($controllers as $controller) {
    if (!file_exists(__DIR__ . "/app/Http/Controllers/Api/$controller.php")) {
        // Comment out the use statement
        $content = preg_replace("/^use App\\\\Http\\\\Controllers\\\\Api\\\\$controller;$/m", "// use App\\Http\\Controllers\\Api\\$controller;", $content);
        // Comment out the route
        $content = preg_replace("/^(.*$controller::class.*)$/m", "// $1", $content);
        echo "Commented out $controller as it does not exist.\n";
    }
}

file_put_contents($file, $content);
echo "Done.\n";
