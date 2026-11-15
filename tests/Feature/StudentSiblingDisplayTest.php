<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Classes;
use App\Models\Section;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentSiblingDisplayTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected $classA;
    protected $sectionA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);

        $this->adminUser = User::create([
            'name'     => 'Admin Tester ' . uniqid(),
            'email'    => 'admin_sibling_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('admin');

        $this->classA = Classes::firstOrCreate(['name' => 'Class X']);
        $this->sectionA = Section::firstOrCreate(['name' => 'Section A']);
    }

    /** @test */
    public function sibling_is_displayed_on_student_page_when_matching_parent_phone()
    {
        $sharedPhone = '98765' . rand(10000, 99999);

        // Student 1
        $student1 = Student::create([
            'first_name'   => 'Alice',
            'last_name'    => 'Sharma',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
            'gender'       => 'Female',
        ]);
        StudentParent::create([
            'student_id'   => $student1->id,
            'father_name'  => 'Raj Sharma',
            'father_phone' => $sharedPhone,
            'mother_name'  => 'Pooja Sharma',
        ]);

        // Student 2 (Sibling)
        $student2 = Student::create([
            'first_name'   => 'Bob',
            'last_name'    => 'Sharma',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
            'gender'       => 'Male',
        ]);
        StudentParent::create([
            'student_id'   => $student2->id,
            'father_name'  => 'Raj Sharma',
            'father_phone' => $sharedPhone,
            'mother_name'  => 'Pooja Sharma',
        ]);

        // Calling GET /api/students/{student1->id}
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/students/{$student1->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('siblings', $data, 'Student response must include siblings array');
        $this->assertCount(1, $data['siblings']);
        $this->assertEquals($student2->id, $data['siblings'][0]['id']);
        $this->assertEquals($student2->admission_no, $data['siblings'][0]['admission_no']);
        $this->assertEquals('Class X', $data['siblings'][0]['class_name']);
        $this->assertEquals('Section A', $data['siblings'][0]['section_name']);

        // Reciprocal: Student 2 page must show Student 1 as sibling
        $response2 = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/students/{$student2->id}");

        $response2->assertStatus(200);
        $data2 = $response2->json('data');
        $this->assertCount(1, $data2['siblings']);
        $this->assertEquals($student1->id, $data2['siblings'][0]['id']);
    }

    /** @test */
    public function sibling_endpoint_returns_siblings_list()
    {
        $sharedEmail = 'parent_' . uniqid() . '@example.com';

        // Student 1
        $student1 = Student::create([
            'first_name'   => 'Charlie',
            'last_name'    => 'Verma',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
        ]);
        StudentParent::create([
            'student_id'   => $student1->id,
            'father_name'  => 'Vikram Verma',
            'father_email' => $sharedEmail,
        ]);

        // Student 2 (Sibling)
        $student2 = Student::create([
            'first_name'   => 'Daisy',
            'last_name'    => 'Verma',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
        ]);
        StudentParent::create([
            'student_id'   => $student2->id,
            'father_name'  => 'Vikram Verma',
            'father_email' => $sharedEmail,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/students/{$student1->id}/siblings");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count'   => 1,
        ]);
        $this->assertEquals($student2->id, $response->json('data.0.id'));
    }

    /** @test */
    public function student_without_siblings_returns_empty_siblings_array()
    {
        $student = Student::create([
            'first_name'   => 'Solo',
            'last_name'    => 'Student',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
        ]);
        StudentParent::create([
            'student_id'   => $student->id,
            'father_name'  => 'Unique Parent ' . uniqid(),
            'father_phone' => '111222' . rand(1000, 9999),
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/students/{$student->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertArrayHasKey('siblings', $data);
        $this->assertIsArray($data['siblings']);
        $this->assertEmpty($data['siblings']);
    }

    /** @test */
    public function soft_deleted_sibling_is_not_displayed()
    {
        $sharedPhone = '99988' . rand(10000, 99999);

        $student1 = Student::create([
            'first_name'   => 'Active',
            'last_name'    => 'Kid',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
        ]);
        StudentParent::create([
            'student_id'   => $student1->id,
            'father_phone' => $sharedPhone,
            'father_name'  => 'Mr Kid',
        ]);

        $student2 = Student::create([
            'first_name'   => 'Deleted',
            'last_name'    => 'Kid',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id'     => $this->classA->id,
            'section_id'   => $this->sectionA->id,
        ]);
        StudentParent::create([
            'student_id'   => $student2->id,
            'father_phone' => $sharedPhone,
            'father_name'  => 'Mr Kid',
        ]);

        // Soft delete student 2
        $student2->delete();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/students/{$student1->id}");

        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.siblings'), 'Soft-deleted sibling must not appear in siblings list');
    }
}
