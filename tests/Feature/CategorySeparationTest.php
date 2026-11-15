<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\AcademicCategory;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategorySeparationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_student_categories_returns_only_social_categories()
    {
        $response = $this->getJson('/api/student-categories');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $data = $response->json('data');
        $names = array_column($data, 'name');

        // Should include OBC, SC, ST, General, Other
        $this->assertContains('OBC', $names);
        $this->assertContains('General', $names);

        // Should NOT include Academic Categories like Rainbow or Galaxy
        $this->assertNotContains('Rainbow - 3', $names);
        $this->assertNotContains('Galaxy HS', $names);
    }

    public function test_academic_categories_returns_academic_levels()
    {
        $response = $this->getJson('/api/academic-categories');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $data = $response->json('data');
        $names = array_column($data, 'name');

        // Should include Rainbow - 3, Planet, etc.
        $this->assertContains('Rainbow - 3', $names);

        // student-academic-categories alias route should also work
        $aliasResponse = $this->getJson('/api/student-academic-categories');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertJson(['success' => true]);
    }

    public function test_student_social_category_is_not_overwritten_by_class()
    {
        $academicYear = AcademicYear::first();
        $obcCategory = StudentCategory::where('name', 'OBC')->first();
        $rainbowCategory = AcademicCategory::where('name', 'Rainbow - 3')->first();

        $class = Classes::create([
            'name' => 'Grade 3 Test',
            'academic_year_id' => $academicYear->id,
            'academic_category_id' => $rainbowCategory->id,
        ]);

        $section = Section::create([
            'class_id' => $class->id,
            'name' => 'A'
        ]);

        // Create student with OBC category
        $student = Student::create([
            'admission_no' => 'TEST_SEP_' . uniqid(),
            'first_name' => 'Rahul',
            'last_name' => 'Kumar',
            'full_name' => 'Rahul Kumar',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'category_id' => $obcCategory->id,
            'academic_category_id' => $rainbowCategory->id,
            'gender' => 'male',
        ]);

        // Student's category should be OBC (social category)
        $this->assertEquals($obcCategory->id, $student->category_id);
        $this->assertEquals('OBC', $student->category->name);

        // Student's academic category should be Rainbow - 3
        $this->assertEquals($rainbowCategory->id, $student->academic_category_id);
        $this->assertEquals('Rainbow - 3', $student->academicCategory->name);

        // Update student without changing category_id - it should NOT be overwritten by class
        $response = $this->postJson("/api/students/{$student->id}", [
            'first_name' => 'Rahul Updated',
            'class_id' => $class->id,
        ]);

        $response->assertStatus(200);
        $student->refresh();

        $this->assertEquals($obcCategory->id, $student->category_id);
        $this->assertEquals('OBC', $student->category->name);
    }
}
