<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\StudentAcademic;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentFilterApiTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $academicYear;
    protected $class;
    protected $section;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->updateOrInsert(['name' => 'admin'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'student'], ['guard_name' => 'web']);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_filter_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);
        $this->admin->assignRole('admin');

        $branchId = DB::table('branches')->where('code', 'MB001')->value('id');
        if (!$branchId) {
            $branchId = DB::table('branches')->insertGetId([
                'name' => 'Main Branch',
                'code' => 'MB001',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->academicYear = AcademicYear::create([
            'name' => '2026-2027 Test Filter',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $this->class = Classes::create([
            'branch_id' => $branchId,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Class Filter Test',
        ]);

        $this->section = Section::create([
            'class_id' => $this->class->id,
            'name' => 'Section Filter Test',
        ]);
    }

    public function test_student_filter_by_class_section_academic_year_and_search()
    {
        // Student 1: Matches search and class/section/academic_year
        $user1 = User::create([
            'name' => 'Vishnu Prakash',
            'email' => 'vishnu_' . uniqid() . '@example.com',
            'password' => bcrypt('password')
        ]);

        $student1 = Student::create([
            'user_id' => $user1->id,
            'admission_no' => 'ADM_VP_1',
            'first_name' => 'Vishnu',
            'last_name' => 'Prakash',
            'full_name' => 'Vishnu Prakash',
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'academic_year_id' => $this->academicYear->id,
            'email' => $user1->email,
        ]);

        // Student 2: Different class
        $otherClass = Classes::create([
            'branch_id' => 1,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Other Class',
        ]);
        $user2 = User::create([
            'name' => 'Other Student',
            'email' => 'other_' . uniqid() . '@example.com',
            'password' => bcrypt('password')
        ]);
        $student2 = Student::create([
            'user_id' => $user2->id,
            'admission_no' => 'ADM_OTHER_1',
            'first_name' => 'Other',
            'last_name' => 'Student',
            'full_name' => 'Other Student',
            'class_id' => $otherClass->id,
            'section_id' => $this->section->id,
            'academic_year_id' => $this->academicYear->id,
            'email' => $user2->email,
        ]);

        // Query API with filters
        $url = sprintf(
            '/api/students?academic_year_id=%d&class_id=%d&section_id=%d&search=%s',
            $this->academicYear->id,
            $this->class->id,
            $this->section->id,
            urlencode('Vishnu Prakash')
        );

        $response = $this->actingAs($this->admin, 'sanctum')->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($student1->id, $data[0]['id']);
        $this->assertEquals('Vishnu Prakash', $data[0]['full_name']);
    }
}
