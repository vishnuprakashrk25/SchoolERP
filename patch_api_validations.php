<?php

$dir = __DIR__ . '/app/Http/Controllers/Api';
$files = glob($dir . '/*.php');
$modelsDir = __DIR__ . '/app/Models';

$patched = 0;

foreach ($files as $file) {
    if (is_dir($file)) continue;

    $content = file_get_contents($file);
    $basename = basename($file, '.php');

    // Skip custom controllers where validation is already fully handled
    if (in_array($basename, ['AuthController', 'UserController', 'StudentController', 'ClassController'])) {
        continue;
    }

    // Check if Validator is already used functionally
    if (strpos($content, 'Validator::make') !== false) {
        continue;
    }

    // Determine model name from string "$item = ModelName::create"
    if (!preg_match('/\$item\s*=\s*([a-zA-Z0-9_]+)::(create|findOrFail)/', $content, $m)) {
        continue; // Cannot determine model reliably
    }
    
    $modelName = $m[1];
    
    // Attempt to parse fillable from model
    $modelFile = $modelsDir . '/' . $modelName . '.php';
    if (!file_exists($modelFile)) {
        continue;
    }

    $modelContent = file_get_contents($modelFile);
    $rules = [];
    if (preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\];/is', $modelContent, $fillMatch)) {
        $fillableStr = $fillMatch[1];
        preg_match_all('/[\'"]([a-zA-Z0-9_]+)[\'"]/', $fillableStr, $fieldsMatch);
        $fields = $fieldsMatch[1];

        foreach ($fields as $f) {
            if (strpos($f, '_id') !== false) {
                $rules[] = "            '$f' => 'required|integer',";
            } elseif (strpos($f, 'email') !== false) {
                $rules[] = "            '$f' => 'required|email',";
            } else {
                $rules[] = "            '$f' => 'required',";
            }
        }
    }

    if (empty($rules)) {
        // Fallback: no fillable properties found
        $rules[] = "            // 'field' => 'required'";
        // continue; // Commented out continue so we still drop an empty validator for Swagger to parse if needed
    }

    $rulesStr = implode("\n", $rules);

    $validationBlock = <<<PHP
        \$validator = \\Illuminate\\Support\\Facades\\Validator::make(\$request->all(), [
$rulesStr
        ]);

        if (\$validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => \$validator->errors()
            ], 422);
        }

PHP;

    // Patch store
    $storePattern = '/(public function store.*?{.*?)(?=\$item\s*=\s*[a-zA-Z0-9_]+::create)/s';
    $newContent = preg_replace($storePattern, "$1" . $validationBlock . '        ', $content, 1);

    // Patch update
    $updatePattern = '/(public function update.*?{.*?\$item\s*=\s*[a-zA-Z0-9_]+::findOrFail.*?\;.*?)(?=\$item->update)/s';
    $newContent = preg_replace($updatePattern, "$1\n" . $validationBlock . '        ', $newContent, 1);

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        $patched++;
        echo "Patched APIs for \$basename\n";
    }
}

echo "Successfully added validation to \$patched generic API controllers.\n";
