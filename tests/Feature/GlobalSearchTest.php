<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\ClassTeacher;
use App\Models\ClassSubject;
use App\Models\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GlobalSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_global_search_returns_aligned_results()
    {
        $user = User::create([
            'name' => 'Search User',
            'email' => 'search_user_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'is_active' => true
        ]);
        
        $class = Classes::create(['name' => '8', 'branch_id' => 1, 'academic_year_id' => 1]);
        $section = Section::create(['name' => 'C', 'class_id' => $class->id]);

        // Create student
        $student = Student::create([
            'first_name' => 'Muhammed',
            'last_name' => 'Shamil',
            'admission_no' => '345363836',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'status' => 1
        ]);

        // Create teacher
        $teacherUser = User::create([
            'name' => 'Muhammed Junaid',
            'email' => 'junaid_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'is_active' => true
        ]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Muhammed',
            'last_name' => 'Junaid',
            'role' => 'Teacher',
            'designation' => 'Mathematics Teacher'
        ]);
        
        ClassTeacher::create([
            'class_id' => $class->id,
            'section_id' => $section->id,
            'teacher_id' => $teacher->id
        ]);
        
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'theory']);
        ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id
        ]);

        // Create Parent
        StudentParent::create([
            'student_id' => $student->id,
            'father_name' => 'Muhammed Raees',
        ]);

        // Create Staff
        Staff::create([
            'first_name' => 'Muhammed',
            'last_name' => 'Sinan',
            'role' => 'Staff',
            'designation' => 'HR'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/search?q=Muhamm');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(4, $data);

        // Verify Student format
        $studentResult = collect($data)->firstWhere('type', 'Student');
        $this->assertNotNull($studentResult);
        $this->assertEquals('Muhammed Shamil', $studentResult['name']);
        $this->assertEquals('Class 8C • Adm No:345363836', $studentResult['details']);

        // Verify Teacher format
        $teacherResult = collect($data)->firstWhere('type', 'Teacher');
        $this->assertNotNull($teacherResult);
        $this->assertEquals('Muhammed Junaid', $teacherResult['name']);
        $this->assertEquals('Class Teacher (8C) • Subject: Mathematics', $teacherResult['details']);

        // Verify Parent format
        $parentResult = collect($data)->firstWhere('type', 'Parents');
        $this->assertNotNull($parentResult);
        $this->assertEquals('Muhammed Raees', $parentResult['name']);
        $this->assertEquals('Parent of Muhammed (8C)', $parentResult['details']);

        // Verify Staff format
        $staffResult = collect($data)->firstWhere('type', 'Staffs');
        $this->assertNotNull($staffResult);
        $this->assertEquals('Muhammed Sinan', $staffResult['name']);
        $this->assertEquals('HR Team', $staffResult['details']);
    }

    public function test_global_search_type_filtering()
    {
        $user = User::create([
            'name' => 'Filter User',
            'email' => 'filter_user_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/search?q=Muhamm&types[]=Student');

        $response->assertStatus(200);
    }
}
