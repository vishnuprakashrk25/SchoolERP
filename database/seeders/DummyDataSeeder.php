<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentCategory;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentAcademic;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\FeeGroup;
use App\Models\FeeType;
use App\Models\Video;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Add User if not exists
        $user = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        // Branch
        $branch = Branch::firstOrCreate(['code' => 'BR_MAIN'], [
            'name' => 'Main Branch',
            'phone' => '1234567890',
            'email' => 'info@mainbranch.com',
            'address' => '123 Main St',
            'status' => 1
        ]);

        // Academic Year
        $year = AcademicYear::firstOrCreate(['name' => '2026-2027'], [
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => 1
        ]);

        // Category
        $category = StudentCategory::firstOrCreate(['name' => 'General'], [
            'description' => 'General Category'
        ]);

        // Class
        $class = Classes::firstOrCreate(['name' => 'Class 10', 'academic_year_id' => $year->id, 'branch_id' => $branch->id], [
            // added here to ensure duplicates aren't created if a different class name exists
        ]);

        // Section
        $section = Section::firstOrCreate(['name' => 'A', 'class_id' => $class->id], [
        ]);

        // Subject
        $subject = Subject::firstOrCreate(['code' => 'MATH10'], [
            'name' => 'Mathematics',
            'type' => 'theory'
        ]);

        // Student
        $student = Student::firstOrCreate(['admission_no' => 'ADM2026001'], [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '0987654321',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'nationality' => 'American',
            'current_address' => '456 Test Ave',
            'permanent_address' => '456 Test Ave'
        ]);

        // Parent
        StudentParent::firstOrCreate(['student_id' => $student->id], [
            'father_name' => 'Michael Doe',
            'father_phone' => '1122334455',
            'mother_name' => 'Sarah Doe',
            'mother_phone' => '5544332211'
        ]);

        // Student Academic
        StudentAcademic::firstOrCreate(['student_id' => $student->id, 'academic_year_id' => $year->id], [
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_no' => '101'
        ]);

        // Teacher
        DB::table('teachers')->insertOrIgnore([
            'id' => 1,
            'user_id' => $user->id,
            'designation' => 'Senior Teacher',
            'qualification' => 'Master of Science',
            'joining_date' => '2020-01-01',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Class Subject
        ClassSubject::firstOrCreate([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => 1 // assuming teacher 1 exists
        ]);


        $this->command->info('Dummy data successfully seeded into the database!');
    }
}
