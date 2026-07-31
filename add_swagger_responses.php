<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

$dir = __DIR__ . '/app/Http/Controllers/Api';
$files = glob($dir . '/*.php');

$successCount = 0;

foreach ($files as $file) {
    if (strpos($file, 'AuthController') !== false) continue; // Skip AuthController

    $content = file_get_contents($file);
    $basename = basename($file, 'Controller.php');
    $modelName = "\\App\\Models\\" . $basename;
    
    // Special Cases
    if ($basename == 'Class') $modelName = "\\App\\Models\\Classes";
    if (!class_exists($modelName)) continue;
    
    try {
        $model = new $modelName();
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);
        
        if (empty($columns)) continue;
        
        $properties = [];
        foreach ($columns as $col) {
            $type = 'string';
            if (in_array($col, ['id', 'user_id', 'class_id', 'section_id', 'branch_id', 'academic_year_id'])) $type = 'integer';
            if (strpos($col, 'is_') !== false || strpos($col, 'status') !== false) $type = 'boolean';
            $properties[] = "     *                  @OA\Property(property=\"$col\", type=\"$type\")";
        }
        $propertiesStr = implode(",\n", $properties);
        
        // Block for single item response (e.g. show, store, update)
        $singleBlock = "     *      @OA\\Response(\n" .
                       "     *          response=200,\n" .
                       "     *          description=\"Successful operation\",\n" .
                       "     *          @OA\\JsonContent(\n" .
                       "     *              @OA\\Property(property=\"success\", type=\"boolean\", example=true),\n" .
                       "     *              @OA\\Property(property=\"message\", type=\"string\", example=\"Success\"),\n" .
                       "     *              @OA\\Property(\n" .
                       "     *                  property=\"data\",\n" .
                       "     *                  type=\"object\",\n" .
                       $propertiesStr . "\n" .
                       "     *              )\n" .
                       "     *          )\n" .
                       "     *       ),";

        // Block for multiple items response (e.g. index)
        $arrayBlock = "     *      @OA\\Response(\n" .
                      "     *          response=200,\n" .
                      "     *          description=\"Successful operation\",\n" .
                      "     *          @OA\\JsonContent(\n" .
                      "     *              @OA\\Property(property=\"success\", type=\"boolean\", example=true),\n" .
                      "     *              @OA\\Property(\n" .
                      "     *                  property=\"data\",\n" .
                      "     *                  type=\"array\",\n" .
                      "     *                  @OA\\Items(\n" .
                      $propertiesStr . "\n" .
                      "     *                  )\n" .
                      "     *              )\n" .
                      "     *          )\n" .
                      "     *       ),";

        $createBlock = str_replace("response=200", "response=201", $singleBlock);

        // Replace basic 200 response in index method context
        // This is tricky because the same pattern exists everywhere.
        // Let's replace only the basic generic 200 responses.
        $generic200 = "/\s*\*\s*@OA\\\\Response\(\s*\*\s*response=200,\s*\*\s*description=\"Successful operation\"\s*\*\s*\),/s";
        $generic201 = "/\s*\*\s*@OA\\\\Response\(\s*\*\s*response=201,\s*\*\s*description=\"Successful operation\"\s*\*\s*\),/s";

        // If the generic 200 is found, we can replace all 201 with single block
        $newContent = preg_replace($generic201, "\n" . $createBlock, $content);

        // For 200, we should differentiate between array and object if possible, 
        // but since it's hard to target specific methods via regex easily without parsing tokens,
        // let's just use the single object block for ALL generic 200s for now, OR
        // better, since index() is the first method, we can do a hack: 
        // first match gets array block, subsequent matches get single block.
        
        $count = 0;
        $newContent = preg_replace_callback($generic200, function($matches) use (&$count, $arrayBlock, $singleBlock) {
            $count++;
            return "\n" . ($count === 1 ? $arrayBlock : $singleBlock);
        }, $newContent);

        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            $successCount++;
            echo "Updated Swagger responses for: " . basename($file) . "\n";
        }
        
    } catch (\Exception $e) {
        // Model or table might not exist
        continue;
    }
}

echo "Successfully updated Swagger responses for $successCount controllers.\n";
