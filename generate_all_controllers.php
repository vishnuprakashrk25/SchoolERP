<?php

$routesFile = __DIR__ . '/routes/api.php';
$content = file_get_contents($routesFile);

// Match both commented and uncommented resource routes
preg_match_all("/\/?\/?\s*Route::apiResource\('([^']+)',\s*([A-Za-z0-9_]+Controller)::class\);/", $content, $matches);

$routesToControllers = [];
for ($i = 0; $i < count($matches[0]); $i++) {
    $path = $matches[1][$i];
    $controller = $matches[2][$i];
    $routesToControllers[$controller] = $path;
}

$generatedCount = 0;
$patchedCount = 0;

foreach ($routesToControllers as $controller => $apiPath) {
    if (in_array($controller, ['AuthController', 'UserController'])) continue;

    $modelName = str_replace('Controller', '', $controller);
    if ($modelName === 'Class') $modelName = 'Classes';
    if ($modelName === 'StudyMaterial') $modelName = 'StudyMaterial';
    // Let's assume the model name mapped directly is correct.

    $controllerPath = __DIR__ . "/app/Http/Controllers/Api/$controller.php";

    if (!file_exists($controllerPath)) {
        echo "Creating $controller for model $modelName...\n";
        exec("php artisan make:controller Api/$controller --api", $output, $returnVar);
        if ($returnVar !== 0) {
            echo "Failed to create $controller.\n";
            continue;
        }
        $generatedCount++;
    }

    $ctrlContent = file_get_contents($controllerPath);
    // If it's effectively empty (no response()), patch it
    if (strpos($ctrlContent, 'return response()->json') === false) {
        echo "Patching $controller with generic CRUD for path /$apiPath...\n";
        
        $stub = <<<PHP
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\\$modelName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class $controller extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/$apiPath",
     *      operationId="get{$modelName}List",
     *      tags={"$modelName"},
     *      summary="Get list of $modelName",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        \$data = $modelName::latest()->get();
        return response()->json([
            'success' => true,
            'data' => \$data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/$apiPath",
     *      operationId="store{$modelName}",
     *      tags={"$modelName"},
     *      summary="Store new $modelName",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#")),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request \$request)
    {
        \$item = $modelName::create(\$request->all());
        return response()->json([
            'success' => true,
            'data' => \$item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/$apiPath/{id}",
     *      operationId="get{$modelName}ById",
     *      tags={"$modelName"},
     *      summary="Get $modelName information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show(\$id)
    {
        \$item = $modelName::findOrFail(\$id);
        return response()->json([
            'success' => true,
            'data' => \$item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/$apiPath/{id}",
     *      operationId="update{$modelName}",
     *      tags={"$modelName"},
     *      summary="Update existing $modelName",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request \$request, \$id)
    {
        \$item = $modelName::findOrFail(\$id);
        \$item->update(\$request->all());
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => \$item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/$apiPath/{id}",
     *      operationId="delete{$modelName}",
     *      tags={"$modelName"},
     *      summary="Delete existing $modelName",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy(\$id)
    {
        \$item = $modelName::findOrFail(\$id);
        \$item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
PHP;
        file_put_contents($controllerPath, $stub);
        $patchedCount++;
    }
}

// Global uncomment for Routes
$content = preg_replace("/\/\/\s*(Route::apiResource\('[^']+',\s*[A-Za-z0-9_]+Controller::class\);)/", "$1", $content);

// Add missing imports
preg_match_all("/Route::apiResource\('[^']+',\s*([A-Za-z0-9_]+Controller)::class\);/", $content, $m);
$ctrls = array_unique($m[1]);
$imports = [];
foreach ($ctrls as $ctrl) {
    if (strpos($content, "use App\Http\Controllers\Api\\$ctrl;") === false && strpos($content, "\\$ctrl") === false) {
        $imports[] = "use App\Http\Controllers\Api\\$ctrl;";
    }
}

if (!empty($imports)) {
   // append the missing imports right before the start of uncommented routes
   $importString = implode("\n", $imports) . "\n";
   $content = preg_replace('/(use App\\\\Http\\\\Controllers\\\\Api\\\\FileController;)/', "$1\n" . $importString, $content);
}

// Clean up any double-commented imports
$content = preg_replace("/\/\/\s*(use App\\\\Http\\\\Controllers\\\\Api\\\\[A-Za-z0-9_]+Controller;)/", "$1", $content);

file_put_contents($routesFile, $content);

echo "Generation process completed. Created $generatedCount controllers. Patched $patchedCount controllers.\n";

