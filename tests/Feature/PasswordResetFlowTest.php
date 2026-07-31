<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\AcademicYear;
use App\Models\ClassRoom; // actually Class model
use App\Models\Section;
use App\Models\Branch;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordResetFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $academicYear;
    protected $class;
    protected $section;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles are seeded (we can manually seed or insert them to be safe)
        DB::table('roles')->updateOrInsert(['name' => 'admin'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'teacher'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'Teacher'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'staff'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'parent'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'student'], ['guard_name' => 'web']);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);
        $this->admin->assignRole('admin');

        // Create standard requirements
        $branchId = DB::table('branches')->where('code', 'MB001')->value('id');
        if (!$branchId) {
            $branchId = DB::table('branches')->insertGetId([
                'name' => 'Main Branch',
                'code' => 'MB001',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->academicYear = DB::table('academic_years')->insertGetId([
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->class = DB::table('classes')->insertGetId([
            'branch_id' => $branchId,
            'academic_year_id' => $this->academicYear,
            'name' => 'Class 10',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->section = DB::table('sections')->insertGetId([
            'class_id' => $this->class,
            'name' => 'A',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_teacher_creation_sends_password_reset_email()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/teachers', [
                'name' => 'Teacher User',
                'email' => 'teacher_' . uniqid() . '@example.com',
                'password' => 'password123',
                'designation' => 'Math Instructor',
                'qualification' => 'M.Sc Mathematics',
                'joining_date' => '2026-06-01'
            ]);

        $response->assertStatus(201);
        $teacherUser = User::where('email', $response->json('data.email'))->first();
        $this->assertNotNull($teacherUser);

        Notification::assertSentTo($teacherUser, ResetPassword::class);
    }

    public function test_staff_creation_sends_password_reset_email()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/staff', [
                'staff_no' => 'STF' . rand(1000, 9999),
                'role' => 'staff',
                'first_name' => 'Staff',
                'last_name' => 'User',
                'email' => 'staff_' . uniqid() . '@example.com'
            ]);

        $response->assertStatus(200);
        $staffUser = User::where('email', $response->json('data.user.email'))->first();
        $this->assertNotNull($staffUser);

        Notification::assertSentTo($staffUser, ResetPassword::class);
    }

    public function test_student_creation_creates_parent_user_and_sends_password_reset()
    {
        Notification::fake();

        $parentEmail = 'parent_' . uniqid() . '@example.com';

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/students', [
                'admission_no' => 'ADM' . rand(1000, 9999),
                'first_name' => 'Student',
                'last_name' => 'One',
                'email' => 'student_' . uniqid() . '@example.com',
                'class_id' => $this->class,
                'section_id' => $this->section,
                'academic_year_id' => $this->academicYear,
                'father_name' => 'Father Name',
                'father_email' => $parentEmail,
                'guardian_is' => 'Father'
            ]);

        $response->assertStatus(200);

        // Assert parent user was created automatically
        $parentUser = User::where('email', $parentEmail)->first();
        $this->assertNotNull($parentUser);
        $this->assertTrue($parentUser->hasRole('parent'));

        // Assert reset password notification was sent to parent
        Notification::assertSentTo($parentUser, ResetPassword::class);
    }
}
