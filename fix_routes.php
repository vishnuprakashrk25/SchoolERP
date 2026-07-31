<?php
$file = __DIR__ . '/routes/api.php';
$content = file_get_contents($file);
preg_match_all('/([A-Za-z0-9_]+Controller)::class/', $content, $matches);
$controllers = array_unique($matches[1]);
$imports = [];
foreach ($controllers as $controller) {
    if (strpos($content, "use App\Http\Controllers\Api\\$controller;") === false) {
        $imports[] = "use App\Http\Controllers\Api\\$controller;";
    }
}
if (!empty($imports)) {
    $importString = implode("\n", $imports) . "\n";
    $content = preg_replace('/(use App\\\\Http\\\\Controllers\\\\Api\\\\ClassController;)/', "$1\n" . $importString, $content);
    file_put_contents($file, $content);
    echo "Added " . count($imports) . " missing controller imports to routes/api.php\n";
} else {
    echo "No imports needed.\n";
}
