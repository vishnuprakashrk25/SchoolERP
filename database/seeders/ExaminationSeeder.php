<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;
use App\Models\Exam;
use App\Models\MarkGrade;

class ExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Exam Types
        $examTypes = [
            ['name' => 'First Term', 'is_group' => true, 'status' => true],
            ['name' => 'Second Term', 'is_group' => true, 'status' => true],
            ['name' => 'Final Term', 'is_group' => true, 'status' => true],
            ['name' => 'Annual Exam', 'is_group' => false, 'status' => true],
        ];

        foreach ($examTypes as $type) {
            ExamType::firstOrCreate(['name' => $type['name']], $type);
            Exam::firstOrCreate(['name' => $type['name']], [
                'is_group' => $type['is_group'],
                'status'   => $type['status']
            ]);
        }

        // 2. Seed Default Mark Grades
        $grades = [
            [
                'grade_name'   => 'A+',
                'gpa'          => 5.0,
                'percent_from' => 80.0,
                'percent_to'   => 100.0,
                'gpa_from'     => 5.0,
                'gpa_to'       => 5.99,
                'description'  => 'Outstanding'
            ],
            [
                'grade_name'   => 'A',
                'gpa'          => 4.0,
                'percent_from' => 70.0,
                'percent_to'   => 79.99,
                'gpa_from'     => 4.0,
                'gpa_to'       => 4.99,
                'description'  => 'Excellent'
            ],
            [
                'grade_name'   => 'A-',
                'gpa'          => 3.5,
                'percent_from' => 60.0,
                'percent_to'   => 69.99,
                'gpa_from'     => 3.5,
                'gpa_to'       => 3.99,
                'description'  => 'Very Good'
            ],
            [
                'grade_name'   => 'B',
                'gpa'          => 3.0,
                'percent_from' => 50.0,
                'percent_to'   => 59.99,
                'gpa_from'     => 3.0,
                'gpa_to'       => 3.49,
                'description'  => 'Good'
            ],
            [
                'grade_name'   => 'C',
                'gpa'          => 2.0,
                'percent_from' => 40.0,
                'percent_to'   => 49.99,
                'gpa_from'     => 2.0,
                'gpa_to'       => 2.99,
                'description'  => 'Satisfactory'
            ],
            [
                'grade_name'   => 'D',
                'gpa'          => 1.0,
                'percent_from' => 33.0,
                'percent_to'   => 39.99,
                'gpa_from'     => 1.0,
                'gpa_to'       => 1.99,
                'description'  => 'Pass'
            ],
            [
                'grade_name'   => 'F',
                'gpa'          => 0.0,
                'percent_from' => 0.0,
                'percent_to'   => 32.99,
                'gpa_from'     => 0.0,
                'gpa_to'       => 0.0,
                'description'  => 'Fail'
            ],
        ];

        foreach ($grades as $grade) {
            MarkGrade::firstOrCreate(['grade_name' => $grade['grade_name']], $grade);
        }
    }
}
