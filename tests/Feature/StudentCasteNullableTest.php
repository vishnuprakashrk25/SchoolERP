<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Student;
use App\Models\AcademicYear;

class StudentCasteNullableTest extends TestCase
{
    protected $user;
    protected $class;
    protected $academicYear;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $this->class = Classes::first();
        $this->academicYear = AcademicYear::first();
    }

    public function test_can_create_student_with_null_caste()
    {
        $admissionNo = 'TEST_ADM_' . rand(10000, 99999);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/students', [
                'admission_no' => $admissionNo,
                'full_name'    => 'Student Without Caste',
                'class_id'     => $this->class->id,
                'section_id'   => null,
                'caste'        => null,
                'religion'     => null,
                'blood_group'  => null,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'admission_no' => $admissionNo,
                    'caste'        => null,
                ]
            ]);

        $this->assertDatabaseHas('students', [
            'admission_no' => $admissionNo,
            'caste'        => null,
        ]);
    }

    public function test_can_update_student_caste_to_null()
    {
        $student = Student::first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/students/{$student->id}", [
                'caste' => null,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id'    => $student->id,
                    'caste' => null,
                ]
            ]);

        $this->assertDatabaseHas('students', [
            'id'    => $student->id,
            'caste' => null,
        ]);
    }
}
