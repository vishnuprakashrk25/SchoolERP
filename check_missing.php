<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$models = glob(__DIR__ . '/app/Models/*.php');
foreach ($models as $modelFile) {
    if (is_dir($modelFile)) continue;
    $modelName = basename($modelFile, '.php');
    $class = 'App\Models\\' . $modelName;
    if (!class_exists($class)) continue;
    try {
        $model = new $class;
        $table = $model->getTable();
        if (!Illuminate\Support\Facades\Schema::hasTable($table)) {
            echo "Missing table: $table (Model: $modelName)\n";
        }
    } catch (\Throwable $e) {}
}
echo "Done checking models.\n";
