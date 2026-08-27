<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentCustomFieldsTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $academicYearId;
    protected $classId;
    protected $sectionId;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->updateOrInsert(['name' => 'admin'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'student'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'parent'], ['guard_name' => 'web']);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test_' . uniqid() . '@example.com',
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

        $this->academicYearId = DB::table('academic_years')->insertGetId([
            'name' => '2026-2027 Test',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->classId = DB::table('classes')->insertGetId([
            'branch_id' => $branchId,
            'academic_year_id' => $this->academicYearId,
            'name' => 'Class X',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->sectionId = DB::table('sections')->insertGetId([
            'class_id' => $this->classId,
            'name' => 'Section A',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_student_creation_stores_apaar_id_aadharshila_no_pen_no()
    {
        $payload = [
            'admission_no' => 'ADM_TEST_' . rand(10000, 99999),
            'first_name' => 'Test',
            'last_name' => 'Student',
            'full_name' => 'Test Student',
            'email' => 'student_custom_' . uniqid() . '@example.com',
            'class_id' => $this->classId,
            'section_id' => $this->sectionId,
            'academic_year_id' => $this->academicYearId,
            'gender' => 'Male',
            'apaar_id' => 'APAAR123456',
            'aadharshila_no' => 'AADHAR987654',
            'pen_no' => 'PEN555666',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/students', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.apaar_id', 'APAAR123456');
        $response->assertJsonPath('data.aadharshila_no', 'AADHAR987654');
        $response->assertJsonPath('data.pen_no', 'PEN555666');

        $studentId = $response->json('data.id');
        $this->assertDatabaseHas('students', [
            'id' => $studentId,
            'apaar_id' => 'APAAR123456',
            'aadharshila_no' => 'AADHAR987654',
            'pen_no' => 'PEN555666',
        ]);
    }

    public function test_student_update_modifies_apaar_id_aadharshila_no_pen_no()
    {
        $student = Student::create([
            'admission_no' => 'ADM_UP_' . rand(10000, 99999),
            'first_name' => 'Update',
            'last_name' => 'Me',
            'full_name' => 'Update Me',
            'class_id' => $this->classId,
            'section_id' => $this->sectionId,
            'academic_year_id' => $this->academicYearId,
            'apaar_id' => 'OLD_APAAR',
            'aadharshila_no' => 'OLD_AADHAR',
            'pen_no' => 'OLD_PEN',
        ]);

        $updatePayload = [
            'full_name' => 'Update Me',
            'apaar_id' => 'NEW_APAAR_99',
            'aadharshila_no' => 'NEW_AADHAR_88',
            'pen_no' => 'NEW_PEN_77',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/students/{$student->id}", $updatePayload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.apaar_id', 'NEW_APAAR_99');
        $response->assertJsonPath('data.aadharshila_no', 'NEW_AADHAR_88');
        $response->assertJsonPath('data.pen_no', 'NEW_PEN_77');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'apaar_id' => 'NEW_APAAR_99',
            'aadharshila_no' => 'NEW_AADHAR_88',
            'pen_no' => 'NEW_PEN_77',
        ]);
    }
}
