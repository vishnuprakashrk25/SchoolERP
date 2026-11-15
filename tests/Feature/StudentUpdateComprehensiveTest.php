<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\AcademicYear;

class StudentUpdateComprehensiveTest extends TestCase
{
    protected $user;
    protected $class;
    protected $section;
    protected $academicYear;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $this->class = Classes::first();
        $this->section = Section::where('class_id', $this->class->id)->first() ?? Section::first();
        $this->academicYear = AcademicYear::first();
    }

    public function test_can_update_student_basic_info_via_put()
    {
        $student = Student::create([
            'admission_no'     => 'UP_ADM_' . rand(10000, 99999),
            'first_name'       => 'InitialFirst',
            'last_name'        => 'InitialLast',
            'full_name'        => 'InitialFirst InitialLast',
            'class_id'         => $this->class->id,
            'section_id'       => $this->section?->id,
            'academic_year_id' => $this->academicYear?->id,
            'roll_no'          => '99',
            'phone'            => '1234567890',
            'gender'           => 'Male',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'full_name'    => 'UpdatedFirst UpdatedLast',
                'first_name'   => 'UpdatedFirst',
                'last_name'    => 'UpdatedLast',
                'roll_no'      => '100',
                'phone'        => '9876543210',
                'gender'       => 'Female',
                'blood_group'  => 'B+',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $this->assertDatabaseHas('students', [
            'id'          => $student->id,
            'full_name'   => 'UpdatedFirst UpdatedLast',
            'roll_no'     => '100',
            'phone'       => '9876543210',
            'gender'      => 'Female',
            'blood_group' => 'B+',
        ]);
    }

    public function test_updating_student_preserves_parent_info_when_not_passed()
    {
        $student = Student::create([
            'admission_no'     => 'PARENT_ADM_' . rand(10000, 99999),
            'first_name'       => 'Student',
            'full_name'        => 'Student With Parent',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        StudentParent::create([
            'student_id'   => $student->id,
            'father_name'  => 'Father John',
            'father_phone' => '1122334455',
            'mother_name'  => 'Mother Mary',
        ]);

        // Update ONLY student's phone and roll_no, without parent fields
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'full_name' => 'Student With Parent',
                'phone'     => '9998887776',
                'roll_no'   => '55',
            ]);

        $response->assertStatus(200);

        // Verify parent details were NOT overwritten with null
        $parent = StudentParent::where('student_id', $student->id)->first();
        $this->assertNotNull($parent);
        $this->assertEquals('Father John', $parent->father_name);
        $this->assertEquals('1122334455', $parent->father_phone);
        $this->assertEquals('Mother Mary', $parent->mother_name);
    }

    public function test_can_update_parent_info_when_explicitly_provided()
    {
        $student = Student::create([
            'admission_no'     => 'P_EXPLICIT_' . rand(10000, 99999),
            'first_name'       => 'Parent',
            'full_name'        => 'Parent Update Student',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'full_name'    => 'Parent Update Student',
                'father_name'  => 'New Father Name',
                'father_phone' => '9988776655',
                'mother_name'  => 'New Mother Name',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('student_parents', [
            'student_id'   => $student->id,
            'father_name'  => 'New Father Name',
            'father_phone' => '9988776655',
            'mother_name'  => 'New Mother Name',
        ]);
    }

    public function test_can_update_student_via_post_route()
    {
        $student = Student::create([
            'admission_no'     => 'POST_ROUTE_' . rand(10000, 99999),
            'first_name'       => 'Old',
            'full_name'        => 'Old Name',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        // POST /api/students/{id} is commonly used for FormData file uploads
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/students/{$student->id}", [
                'full_name' => 'Updated via POST route',
                'note'      => 'Updated note',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('students', [
            'id'        => $student->id,
            'full_name' => 'Updated via POST route',
            'note'      => 'Updated note',
        ]);
    }

    public function test_updating_full_name_syncs_first_last_and_user_name()
    {
        $user = User::create([
            'name'      => 'Old User',
            'full_name' => 'Old User',
            'email'     => 'testuser_' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $student = Student::create([
            'user_id'          => $user->id,
            'admission_no'     => 'SYNC_NAME_' . rand(10000, 99999),
            'first_name'       => 'Old',
            'last_name'        => 'User',
            'full_name'        => 'Old User',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'full_name' => 'Alexander Hamilton',
            ]);

        $response->assertStatus(200);

        $student->refresh();
        $this->assertEquals('Alexander Hamilton', $student->full_name);
        // first_name and last_name are independent and should NOT be altered by full_name
        $this->assertEquals('Old', $student->first_name);
        $this->assertEquals('User', $student->last_name);

        $user->refresh();
        $this->assertEquals('Alexander Hamilton', $user->name);
        $this->assertEquals('Alexander Hamilton', $user->full_name);
    }

    public function test_updating_student_email_syncs_user_email()
    {
        $user = User::create([
            'name'      => 'Email Student',
            'full_name' => 'Email Student',
            'email'     => 'initial_student_' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $student = Student::create([
            'user_id'          => $user->id,
            'admission_no'     => 'SYNC_EMAIL_' . rand(10000, 99999),
            'first_name'       => 'Email',
            'last_name'        => 'Student',
            'full_name'        => 'Email Student',
            'email'            => $user->email,
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        $newEmail = 'updated_student_' . uniqid() . '@example.com';
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'email' => $newEmail,
            ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals($newEmail, $user->email);

        // Also test clearing/nullifying email
        $response2 = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'email' => '',
            ]);
        $response2->assertStatus(200);
        $user->refresh();
        $this->assertNull($user->email);
    }

    public function test_can_update_with_first_name_and_last_name_without_full_name()
    {
        $student = Student::create([
            'admission_no'     => 'FIRST_LAST_' . rand(10000, 99999),
            'first_name'       => 'OldFirst',
            'last_name'        => 'OldLast',
            'full_name'        => 'Custom Full Name',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        // Send only first_name and last_name
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'admission_no' => 'ADM12365_TEST_' . rand(10000, 99999),
                'roll_no'      => '101',
                'first_name'   => 'sree',
                'last_name'    => 'Doe',
            ]);

        $response->assertStatus(200);
        $student->refresh();
        // full_name is independent and should NOT be altered by first_name and last_name
        $this->assertEquals('Custom Full Name', $student->full_name);
        $this->assertEquals('sree', $student->first_name);
        $this->assertEquals('Doe', $student->last_name);
        $this->assertEquals('101', $student->roll_no);
    }

    public function test_updating_nonexistent_student_returns_404_with_helpful_message()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/students/999999', [
                'first_name' => 'Ghost',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('Student not found with ID 999999', $response->json('message'));
    }

    public function test_update_with_empty_strings_converts_to_null_and_succeeds_without_sql_1366_error()
    {
        $student = Student::create([
            'admission_no'     => 'ADM_' . rand(10000, 99999),
            'first_name'       => 'OldFirst',
            'last_name'        => 'Doe',
            'full_name'        => 'OldFirst Doe',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        $payload = [
            'admission_no'            => 'ADM129_' . rand(100, 999),
            'admission_date'          => '2026-11-03',
            'first_name'              => 'vprk',
            'last_name'               => 'Doe',
            'email'                   => '',
            'class_id'                => '',
            'section_id'              => '',
            'previous_school_details' => 'Previous School Name',
            'note'                    => 'Some note',
            'is_disabled'             => 0,
            'disable_reason'          => '',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $student->refresh();
        $this->assertEquals('vprk', $student->first_name);
        $this->assertNull($student->class_id);
        $this->assertNull($student->section_id);
        $this->assertNull($student->email);
        $this->assertNull($student->disable_reason);
    }

    public function test_post_update_with_empty_strings_succeeds()
    {
        $student = Student::create([
            'admission_no'     => 'POST_ADM_' . rand(10000, 99999),
            'first_name'       => 'OldPost',
            'last_name'        => 'Doe',
            'full_name'        => 'OldPost Doe',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        $payload = [
            'admission_no'   => 'ADM_POST_' . rand(100, 999),
            'admission_date' => '2026-11-03',
            'first_name'     => 'vprk_post',
            'last_name'      => 'Doe',
            'email'          => '',
            'class_id'       => '',
            'section_id'     => '',
            'disable_reason' => '',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->post("/api/students/{$student->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $student->refresh();
        $this->assertEquals('vprk_post', $student->first_name);
        $this->assertNull($student->class_id);
    }

    public function test_can_create_student_with_empty_class_id_and_empty_strings()
    {
        $payload = [
            'admission_no'   => 'ADM_STORE_' . rand(10000, 99999),
            'admission_date' => '2026-11-03',
            'first_name'     => 'StoreStudent',
            'last_name'      => 'Test',
            'email'          => '',
            'class_id'       => '',
            'section_id'     => '',
            'gender'         => 'male',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/students', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $created = Student::where('admission_no', $payload['admission_no'])->first();
        $this->assertNotNull($created);
        $this->assertNull($created->class_id);
        $this->assertNull($created->section_id);
        $this->assertNull($created->email);
    }

    public function test_updating_student_with_empty_or_null_admission_no_returns_422_validation_error()
    {
        $originalAdmNo = 'ADM_TEST_' . rand(10000, 99999);
        $student = Student::create([
            'admission_no'     => $originalAdmNo,
            'first_name'       => 'KeepAdm',
            'last_name'        => 'Student',
            'full_name'        => 'KeepAdm Student',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        // Attempt update with empty string admission_no -> returns clean 422 validation error
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'admission_no' => '',
                'full_name'    => 'Renamed KeepAdm Student',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'admission_no' => [
                        'The admission number cannot be null or empty.'
                    ]
                ]
            ]);

        // Attempt update with null admission_no -> returns clean 422 validation error
        $responseNull = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'admission_no' => null,
            ]);

        $responseNull->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'admission_no' => [
                        'The admission number cannot be null or empty.'
                    ]
                ]
            ]);
    }

    public function test_updating_student_without_passing_admission_no_succeeds_and_preserves_it()
    {
        $originalAdmNo = 'ADM_PRESERVE_' . rand(10000, 99999);
        $student = Student::create([
            'admission_no'     => $originalAdmNo,
            'first_name'       => 'KeepAdm',
            'last_name'        => 'Student',
            'full_name'        => 'KeepAdm Student',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear?->id,
        ]);

        // When admission_no is not passed at all, update succeeds
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$student->id}", [
                'full_name' => 'Renamed Safely',
                'phone'     => '9876543210',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $student->refresh();
        $this->assertEquals($originalAdmNo, $student->admission_no);
        $this->assertEquals('Renamed Safely', $student->full_name);
    }
}


