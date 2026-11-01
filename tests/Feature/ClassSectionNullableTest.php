<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAcademic;
use App\Models\ClassTeacher;
use App\Models\Homework;

class ClassSectionNullableTest extends TestCase
{
    protected $user;
    protected $academicYear;
    protected $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $this->academicYear = AcademicYear::first();
        $this->class = Classes::first();
    }

    public function test_can_create_standalone_section_with_null_class_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/sections', [
                'name'     => 'Section Global X',
                'class_id' => null,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name'     => 'Section Global X',
                    'class_id' => null,
                ]
            ]);

        $sectionId = $response->json('data.id');
        $this->assertDatabaseHas('sections', [
            'id'       => $sectionId,
            'name'     => 'Section Global X',
            'class_id' => null,
        ]);
    }

    public function test_can_create_class_with_null_sections()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/classes', [
                'name'             => 'Class Standalone',
                'academic_year_id' => $this->academicYear->id,
                'sections'         => null,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Class Standalone',
                ]
            ]);
    }

    public function test_can_create_homework_with_null_section()
    {
        $subject = Subject::first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/homeworks', [
                'class_id'        => $this->class->id,
                'section_id'      => null,
                'subject_id'      => $subject->id,
                'homework_date'   => now()->format('Y-m-d'),
                'submission_date' => now()->addDays(3)->format('Y-m-d'),
                'marks'           => 50,
                'description'     => 'Homework for entire class with no section',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'class_id'   => $this->class->id,
                    'section_id' => null,
                ]
            ]);
    }

    public function test_can_assign_class_teacher_with_null_section()
    {
        $teacher = Staff::first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/class-teachers', [
                'class_id'    => $this->class->id,
                'section_id'  => null,
                'teacher_ids' => [$teacher->id],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Teachers assigned to class successfully',
            ]);

        $this->assertDatabaseHas('class_teachers', [
            'class_id'   => $this->class->id,
            'section_id' => null,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_can_save_student_academic_with_null_section()
    {
        $student = Student::first();

        $record = StudentAcademic::create([
            'student_id'       => $student->id,
            'class_id'         => $this->class->id,
            'section_id'       => null,
            'academic_year_id' => $this->academicYear->id,
            'roll_no'          => '999',
        ]);

        $this->assertDatabaseHas('student_academics', [
            'id'         => $record->id,
            'section_id' => null,
        ]);
    }
}
