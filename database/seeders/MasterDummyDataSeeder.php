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
use App\Models\Teacher;
use App\Models\ClassTeacher;
use App\Models\Staff;
use App\Models\StaffDepartment;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\FeeGroup;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\Assignment;
use App\Models\StudyMaterial;
use App\Models\Timetable;
use App\Models\Notification;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\CertificateTemplate;
use App\Models\Admission\AdmissionQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SafeDummyGenerator
{
    public function __get($prop)
    {
        return match ($prop) {
            'name' => 'User ' . mt_rand(100, 999),
            'firstName' => 'Member' . mt_rand(10, 99),
            'lastName' => 'Demo',
            'phoneNumber' => '98' . mt_rand(10000000, 99999999),
            'safeEmail' => 'user' . uniqid() . '@example.com',
            'address' => 'Sample Address #' . mt_rand(1, 100),
            default => 'Sample ' . $prop,
        };
    }

    public function __call($method, $args)
    {
        return match ($method) {
            'name' => 'Person ' . mt_rand(100, 999),
            'date' => date('Y-m-d', strtotime('-' . mt_rand(5, 15) . ' years')),
            'unique' => $this,
            'randomElement' => !empty($args[0]) ? $args[0][array_rand($args[0])] : 'male',
            default => 'Sample ' . $method,
        };
    }
}

class MasterDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = class_exists(\Faker\Factory::class) ? \Faker\Factory::create() : new SafeDummyGenerator();

        // 1. ROLES & PERMISSIONS
        $this->seedRoles();

        // 2. BRANCHES & ACADEMIC YEARS
        $branch = Branch::firstOrCreate(['code' => 'MAIN'], [
            'name' => 'Main Campus',
            'email' => 'admin@school.com',
            'phone' => '1234567890',
            'address' => '123 School Lane',
            'status' => 1
        ]);
        
        $branch2 = Branch::firstOrCreate(['code' => 'CITY'], [
            'name' => 'City Branch',
            'email' => 'city@school.com',
            'phone' => '0987654321',
            'address' => '456 City Road',
            'status' => 1
        ]);

        $year = AcademicYear::firstOrCreate(['name' => '2026-2027'], [
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => 1
        ]);

        // 3. DEPARTMENTS & CATEGORIES
        $depts = ['Academic', 'Administration', 'Accounts', 'Library', 'Transport'];
        foreach ($depts as $dept) {
            StaffDepartment::firstOrCreate(['name' => $dept]);
        }
        $academicDept = StaffDepartment::where('name', 'Academic')->first();

        $cats = ['General', 'OBC', 'SC', 'ST'];
        foreach ($cats as $cat) {
            StudentCategory::firstOrCreate(['name' => $cat]);
        }
        $genCat = StudentCategory::where('name', 'General')->first();

        // 4. CLASSES & SECTIONS
        $classNames = ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'];
        $sections = ['A', 'B'];
        
        $firstClass = null;
        $firstSection = null;

        foreach ($classNames as $className) {
            $class = Classes::firstOrCreate(['name' => $className, 'branch_id' => $branch->id, 'academic_year_id' => $year->id]);
            foreach ($sections as $secName) {
                $section = Section::firstOrCreate(['name' => $secName, 'class_id' => $class->id]);
                if (!$firstClass) $firstClass = $class;
                if (!$firstSection) $firstSection = $section;
            }
        }

        // 5. SUBJECTS
        $subjectNames = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'theory'],
            ['name' => 'Science', 'code' => 'SCI', 'type' => 'theory'],
            ['name' => 'English', 'code' => 'ENG', 'type' => 'theory'],
            ['name' => 'History', 'code' => 'HIST', 'type' => 'theory'],
            ['name' => 'Physics Lab', 'code' => 'PHY_LAB', 'type' => 'practical'],
        ];
        foreach ($subjectNames as $sub) {
            Subject::firstOrCreate(['code' => $sub['code']], $sub);
        }
        $mathSub = Subject::where('code', 'MATH')->first();

        // 6. USERS, STAFF & TEACHERS
        // Admin
        $adminUser = User::firstOrCreate(['email' => 'admin@admin.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true
        ]);
        $adminUser->assignRole('admin');

        // Teachers
        for ($i = 1; $i <= 5; $i++) {
            $email = "teacher{$i}@school.com";
            $user = User::firstOrCreate(['email' => $email], [
                'name' => $faker->name,
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'is_active' => true
            ]);
            $user->assignRole('teacher');

            Teacher::firstOrCreate(['user_id' => $user->id], [
                'designation' => 'Senior Teacher',
                'qualifications' => 'MA/MSc',
                'date_of_joining' => $faker->date(),
            ]);
        }
        $firstTeacher = Teacher::first();

        // Staff
        for ($i = 1; $i <= 3; $i++) {
            Staff::firstOrCreate(['email' => "staff{$i}@school.com"], [
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => $faker->phoneNumber,
                'designation' => 'Assistant',
            ]);
        }

        // 7. ASSIGNMENTS
        // Class Teacher
        ClassTeacher::firstOrCreate([
            'class_id' => $firstClass->id,
            'section_id' => $firstSection->id,
            'teacher_id' => $firstTeacher->id
        ]);

        // Class Subject
        foreach (Classes::all() as $cls) {
            ClassSubject::firstOrCreate([
                'class_id' => $cls->id,
                'subject_id' => $mathSub->id,
                'teacher_id' => $firstTeacher->id
            ]);
        }

        // 8. STUDENTS & PARENTS
        for ($i = 1; $i <= 10; $i++) {
            $admNo = "STU" . str_pad($i, 4, '0', STR_PAD_LEFT);
            $student = Student::firstOrCreate(['admission_no' => $admNo], [
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'dob' => $faker->date('Y-m-d', '2015-01-01'),
                'gender' => $faker->randomElement(['male', 'female']),
                'current_address' => $faker->address,
                'permanent_address' => $faker->address,
                'category_id' => $genCat->id,
            ]);

            StudentParent::firstOrCreate(['student_id' => $student->id], [
                'father_name' => $faker->name('male'),
                'father_phone' => $faker->phoneNumber,
                'mother_name' => $faker->name('female'),
                'mother_phone' => $faker->phoneNumber,
            ]);

            StudentAcademic::firstOrCreate([
                'student_id' => $student->id,
                'academic_year_id' => $year->id
            ], [
                'class_id' => $firstClass->id,
                'section_id' => $firstSection->id,
                'roll_no' => $i
            ]);
        }

        // 9. FINANCIALS
        $feeType = FeeType::firstOrCreate(['name' => 'Tuition Fee']);
        
        FeeStructure::firstOrCreate([
            'class_id' => $firstClass->id,
            'fee_type_id' => $feeType->id
        ], [
            'amount' => 5000.00
        ]);

        // 10. EXAMS
        $exam = Exam::firstOrCreate(['name' => 'First Term Exam'], [
            'academic_year_id' => $year->id,
        ]);
        
        ExamSchedule::firstOrCreate([
            'exam_id' => $exam->id,
            'class_id' => $firstClass->id,
            'subject_id' => $mathSub->id
        ], [
            'exam_date' => '2026-09-15',
        ]);

        // 11. MISC
        Notification::create([
            'user_id' => $adminUser->id,
            'title' => 'School Reopening',
            'message' => 'The school will reopen on April 1st.',
        ]);

        $this->command->info('Master dummy data successfully seeded!');
    }

    private function seedRoles()
    {
        $roles = ['super-admin', 'admin', 'teacher', 'staff', 'student', 'parent'];
        $guards = ['web', 'sanctum'];

        foreach ($guards as $guard) {
            foreach ($roles as $role) {
                Role::firstOrCreate(['name' => $role, 'guard_name' => $guard]);
            }

            $perms = ['manage_admission', 'view_reports', 'manage_academics', 'manage_fees'];
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
            }

            $admin = Role::where('name', 'admin')->where('guard_name', $guard)->first();
            if ($admin) {
                $guardPerms = Permission::where('guard_name', $guard)->get();
                $admin->syncPermissions($guardPerms);
            }
        }
    }
}
