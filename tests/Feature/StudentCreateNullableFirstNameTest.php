<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentCreateNullableFirstNameTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_student_with_null_first_name()
    {
        $academicYear = AcademicYear::first();

        $response = $this->postJson('/api/students', [
            'admission_no' => 'TEST_NULL_FN_' . uniqid(),
            'full_name' => 'Johnathon Smith',
            'first_name' => null,
            'last_name' => null,
            'academic_year_id' => $academicYear ? $academicYear->id : null,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $studentId = $response->json('data.id');
        $student = Student::find($studentId);

        $this->assertNotNull($student);
        $this->assertNull($student->first_name);
        $this->assertEquals('Johnathon Smith', $student->full_name);
    }

    public function test_can_create_student_with_empty_string_first_name()
    {
        $academicYear = AcademicYear::first();

        $response = $this->postJson('/api/students', [
            'admission_no' => 'TEST_EMPTY_FN_' . uniqid(),
            'full_name' => 'Alice Walker',
            'first_name' => '',
            'last_name' => '',
            'academic_year_id' => $academicYear ? $academicYear->id : null,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $studentId = $response->json('data.id');
        $student = Student::find($studentId);

        $this->assertNotNull($student);
        $this->assertNull($student->first_name);
        $this->assertEquals('Alice Walker', $student->full_name);
    }
}
