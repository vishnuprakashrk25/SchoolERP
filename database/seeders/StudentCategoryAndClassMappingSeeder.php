<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentCategory;
use App\Models\Classes;

class StudentCategoryAndClassMappingSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Rainbow - 3', 'description' => 'Only 3rd Class'],
            ['name' => 'Rainbow - 4', 'description' => 'Only 4th Class'],
            ['name' => 'Rainbow - 5', 'description' => 'Only 5th Class'],
            ['name' => 'Planet', 'description' => '6th to 8th Class'],
            ['name' => 'Galaxy HS', 'description' => '9th to 10th Class'],
            ['name' => 'Galaxy HSS (Arts)', 'description' => '11th and 12th Class (Arts Only)'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = StudentCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }

        // Map existing classes to categories based on name patterns
        $mappings = [
            'Rainbow - 3' => ['Class 3', 'Grade 3', '3rd'],
            'Rainbow - 4' => ['Class 4', 'Grade 4', '4th'],
            'Rainbow - 5' => ['Class 5', 'Grade 5', '5th'],
            'Planet' => ['Class 6', 'Grade 6', 'Class 7', 'Grade 7', 'Class 8', 'Grade 8'],
            'Galaxy HS' => ['Class 9', 'Grade 9', 'Class 10', 'Grade 10', 'Class 10 Updated', 'Class X'],
            'Galaxy HSS (Arts)' => ['Class 11 Arts', 'Grade 11 Arts', 'Class 12 Arts', 'Grade 12 Arts', '+1 Arts', '+2 Arts'],
        ];

        foreach ($mappings as $catName => $patterns) {
            $catId = $categoryModels[$catName]->id;
            foreach ($patterns as $pattern) {
                Classes::where('name', $pattern)
                    ->orWhere('name', 'like', $pattern . ' %')
                    ->update(['category_id' => $catId]);
            }
        }
    }
}
