<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentAcademic;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendTestEmailCommand extends Command
{
    protected $signature = 'app:send-test-reset';
    protected $description = 'Creates a test teacher and student/parent user with rkvishnuprakash@gmail.com and sends password reset emails';

    public function handle()
    {
        $email = 'rkvishnuprakash@gmail.com';
        $this->info("Starting test setup for: {$email}");

        // Clean existing users to avoid unique constraints
        User::where('email', $email)->delete();

        // 1. Setup roles
        DB::table('roles')->updateOrInsert(['name' => 'Teacher'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'parent'], ['guard_name' => 'web']);
        DB::table('roles')->updateOrInsert(['name' => 'student'], ['guard_name' => 'web']);

        // Get or create branch
        $branchId = DB::table('branches')->value('id');
        if (!$branchId) {
            $branchId = DB::table('branches')->insertGetId([
                'name' => 'Test Branch',
                'code' => 'TB001',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get or create academic year
        $academicYearId = DB::table('academic_years')->value('id');
        if (!$academicYearId) {
            $academicYearId = DB::table('academic_years')->insertGetId([
                'name' => '2026-2027',
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31',
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get or create class
        $classId = DB::table('classes')->value('id');
        if (!$classId) {
            $classId = DB::table('classes')->insertGetId([
                'branch_id' => $branchId,
                'academic_year_id' => $academicYearId,
                'name' => 'Class 10',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get or create section
        $sectionId = DB::table('sections')->value('id');
        if (!$sectionId) {
            $sectionId = DB::table('sections')->insertGetId([
                'class_id' => $classId,
                'name' => 'A',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 2. Create Teacher
        $this->info("Creating Teacher...");
        $teacherUser = User::create([
            'name' => 'Test Teacher',
            'email' => $email,
            'password' => Hash::make('password123'),
            'branch_id' => $branchId,
            'is_active' => true,
        ]);
        $teacherUser->assignRole('Teacher');

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'staff_no' => 'TCH-' . str_pad($teacherUser->id, 4, '0', STR_PAD_LEFT),
            'role' => 'Teacher',
            'first_name' => 'Test',
            'last_name' => 'Teacher',
            'email' => $email,
            'designation' => 'Instructor',
        ]);

        // Send reset email for teacher
        try {
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $email]);
            $this->info("Password reset email sent to Teacher: {$email}");
        } catch (\Exception $e) {
            $this->error("Failed sending reset link to teacher: " . $e->getMessage());
        }

        // Clean user again for parent test (since same email is used)
        User::where('email', $email)->delete();

        // 3. Create Student (triggers parent user auto-creation)
        $this->info("Creating Student and Parent user...");
        $studentUser = User::create([
            'name' => 'Test Student',
            'email' => 'student_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'branch_id' => $branchId,
            'is_active' => true,
        ]);
        $studentUser->assignRole('student');

        $student = Student::create([
            'user_id' => $studentUser->id,
            'admission_no' => 'ADM' . rand(1000, 9999),
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => $studentUser->email,
            'class_id' => $classId,
            'section_id' => $sectionId,
        ]);

        StudentAcademic::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYearId,
            'class_id' => $classId,
            'section_id' => $sectionId
        ]);

        // This will trigger StudentParent::create, parent User auto-creation, and sending password reset email
        StudentParent::create([
            'student_id' => $student->id,
            'father_name' => 'Father Prakash',
            'father_phone' => '1234567890',
            'father_email' => $email,
            'guardian_is' => 'Father',
        ]);

        // Auto-create parent user logic (same as in StudentController)
        $parentUser = User::where('email', $email)->first();
        if (!$parentUser) {
            $parentUser = User::create([
                'name' => 'Father Prakash',
                'email' => $email,
                'password' => Hash::make(Str::random(16)),
                'is_active' => true,
            ]);
            $parentUser->assignRole('parent');
        }

        try {
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $email]);
            $this->info("Password reset email sent to Parent: {$email}");
        } catch (\Exception $e) {
            $this->error("Failed sending reset link to parent: " . $e->getMessage());
        }

        $this->info("Done! Please check your email inbox / Mailpit / logs.");
    }
}
