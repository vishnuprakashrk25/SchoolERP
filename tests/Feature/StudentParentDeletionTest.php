<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentParent;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentParentDeletionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function parent_is_kept_if_they_have_another_enrolled_student()
    {
        $parentEmail = 'parent_with_two_kids_' . uniqid() . '@example.com';
        $parentUser = User::create([
            'name' => 'Multi Parent',
            'email' => $parentEmail,
            'password' => bcrypt('password'),
        ]);
        $parentUser->assignRole('parent');

        // Student 1
        $student1 = Student::create([
            'first_name' => 'Child One',
            'last_name' => 'Test',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id' => 1,
            'section_id' => 1,
        ]);
        StudentParent::create([
            'student_id' => $student1->id,
            'father_name' => 'Multi Parent',
            'father_email' => $parentEmail,
        ]);

        // Student 2 (Sibling)
        $student2 = Student::create([
            'first_name' => 'Child Two',
            'last_name' => 'Test',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id' => 1,
            'section_id' => 1,
        ]);
        StudentParent::create([
            'student_id' => $student2->id,
            'father_name' => 'Multi Parent',
            'father_email' => $parentEmail,
        ]);

        // Delete Student 1
        $student1->delete();

        // Assert Student 1 is deleted
        $this->assertSoftDeleted('students', ['id' => $student1->id]);

        // Assert Parent User is STILL ACTIVE (KEPT) because Student 2 is still enrolled
        $this->assertDatabaseHas('users', [
            'id' => $parentUser->id,
            'email' => $parentEmail,
            'deleted_at' => null,
        ]);
        $this->assertTrue($parentUser->fresh()->hasRole('parent'));
    }

    /** @test */
    public function parent_is_deleted_when_their_only_student_is_deleted()
    {
        $parentEmail = 'parent_single_kid_' . uniqid() . '@example.com';
        $parentUser = User::create([
            'name' => 'Single Parent',
            'email' => $parentEmail,
            'password' => bcrypt('password'),
        ]);
        $parentUser->assignRole('parent');

        $student = Student::create([
            'first_name' => 'Only Child',
            'last_name' => 'Test',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id' => 1,
            'section_id' => 1,
        ]);
        StudentParent::create([
            'student_id' => $student->id,
            'father_name' => 'Single Parent',
            'father_email' => $parentEmail,
        ]);

        // Delete the only student
        $student->delete();

        // Assert Student is deleted
        $this->assertSoftDeleted('students', ['id' => $student->id]);

        // Assert Parent User is DELETED
        $this->assertSoftDeleted('users', ['id' => $parentUser->id]);
    }

    /** @test */
    public function staff_parent_account_is_kept_and_only_parent_role_is_revoked()
    {
        $staffParentEmail = 'staff_parent_' . uniqid() . '@example.com';
        $staffParentUser = User::create([
            'name' => 'Teacher Parent',
            'email' => $staffParentEmail,
            'password' => bcrypt('password'),
        ]);
        $staffParentUser->assignRole('teacher');
        $staffParentUser->assignRole('parent');

        $student = Student::create([
            'first_name' => 'Staff Child',
            'last_name' => 'Test',
            'admission_no' => 'ADM_' . uniqid(),
            'class_id' => 1,
            'section_id' => 1,
        ]);
        StudentParent::create([
            'student_id' => $student->id,
            'father_name' => 'Teacher Parent',
            'father_email' => $staffParentEmail,
        ]);

        // Delete the only student
        $student->delete();

        // Assert Student is deleted
        $this->assertSoftDeleted('students', ['id' => $student->id]);

        // Assert User is NOT deleted because they are a teacher
        $this->assertDatabaseHas('users', [
            'id' => $staffParentUser->id,
            'deleted_at' => null,
        ]);

        // Assert 'parent' role was revoked, but 'teacher' role is retained
        $this->assertFalse($staffParentUser->fresh()->hasRole('parent'));
        $this->assertTrue($staffParentUser->fresh()->hasRole('teacher'));
    }
}
