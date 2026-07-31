<?php

$dir = __DIR__ . '/app/Http/Controllers/Api';
$files = glob($dir . '/*.php');

$successCount = 0;

foreach ($files as $file) {
    if (strpos($file, 'AuthController') !== false) continue; // Skip AuthController as it has custom logic

    $content = file_get_contents($file);

    // Extract validation rules from store() method
    // Looking for Validator::make($request->all(), [ ... ])
    if (preg_match('/(?:\\\\?Illuminate\\\\Support\\\\Facades\\\\)?Validator::make\(\$request->all\(\)\s*,\s*\[(.*?)\]\)/s', $content, $matches) || 
        preg_match('/(?:\\\\?Illuminate\\\\Support\\\\Facades\\\\)?Validator::make\(\$request->all\(\)\s*,\s*\[(.*?)\]\)/s', $content, $matches)) {
        
        $rulesBlock = $matches[1];
        
        // Extract keys
        preg_match_all('/([\'"])([a-zA-Z0-9_\-]+)\1\s*=>/', $rulesBlock, $keyMatches);
        $fields = $keyMatches[2];
        
        if (empty($fields)) continue;

        $properties = [];
        $required = [];

        foreach ($fields as $field) {
            // Determine required status (assume all required string for simplicity, unless we parse the rules)
            // But let's check if 'required' is in the rule string
            preg_match("/(['\"])$field\\1\s*=>\s*(['\"])(.*?)\\2/", $rulesBlock, $ruleMatches);
            $ruleStr = $ruleMatches[3] ?? '';
            
            $type = 'string';
            $example = $field;

            if (strpos($ruleStr, 'integer') !== false || strpos($field, '_id') !== false) {
                $type = 'integer';
                $example = "1";
            } elseif (strpos($ruleStr, 'boolean') !== false) {
                $type = 'boolean';
                $example = "true";
            } elseif (strpos($ruleStr, 'date') !== false) {
                $type = 'string';
                $example = "2024-01-01";
            }

            if (strpos($ruleStr, 'nullable') === false && strpos($ruleStr, 'required') !== false) {
                $required[] = '"' . $field . '"';
            } else {
                // If no rule string explicitly says nullable and we aren't sure, assume required if no nullable
                if (strpos($ruleStr, 'nullable') === false) {
                    $required[] = '"' . $field . '"';
                }
            }

            $properties[] = "     *              @OA\\Property(property=\"$field\", type=\"$type\", example=\"$example\")";
        }

        $requiredStr = !empty($required) ? "     *              required={" . implode(",", $required) . "}," . "\n" : "";
        $propertiesStr = implode(",\n", $properties);

        $replacement = "     *      @OA\\RequestBody(\n" .
                       "     *          required=true,\n" .
                       "     *          @OA\\JsonContent(\n" .
                       $requiredStr .
                       $propertiesStr . "\n" .
                       "     *          )\n" .
                       "     *      ),";

        $pattern = '/\s*\*\s*@OA\\\\RequestBody\(\s*(?:\*\s*)?required=true,\s*(?:\*\s*)?@OA\\\\JsonContent\(ref=\"#\"\)\s*(?:\*\s*)?\),/s';
        
        $newContent = preg_replace($pattern, "\n" . $replacement, $content);
        
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            $successCount++;
            echo "Updated Swagger config down for: " . basename($file) . "\n";
        }
    } else {
        // Fallback for missing validators but has ref="#"
        $pattern = '/\s*\*\s*@OA\\\\RequestBody\(\s*(?:\*\s*)?required=true,\s*(?:\*\s*)?@OA\\\\JsonContent\(ref=\"#\"\)\s*(?:\*\s*)?\),/s';
        $fallback = "     *      @OA\\RequestBody(\n" .
                    "     *          required=false,\n" .
                    "     *          @OA\\JsonContent(type=\"object\")\n" .
                    "     *      ),";
        $newContent = preg_replace($pattern, "\n" . $fallback, $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            $successCount++;
            echo "Updated Swagger fallback config for: " . basename($file) . "\n";
        }
    }
}

echo "Successfully updated Swagger annotations for $successCount controllers.\n";
