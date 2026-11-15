<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamResult\SaveExamResultRequest;
use App\Http\Requests\GeneralSetting\UpdateGeneralSettingsRequest;
use App\Http\Requests\StudentFee\AssignStudentFeeRequest;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\FeeType;
use App\Models\GeneralSetting;
use App\Models\SidebarMenu;
use App\Models\SidebarSubMenu;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeItem;
use App\Services\FeeAssignmentService;
use App\Services\StudentPhotoService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class OptimizationRefactorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
        ]);
        DB::purge('sqlite');
        Cache::flush();
    }

    public function test_index_migration_creates_and_drops_indexes_idempotently(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->date('date')->nullable();
            $table->string('status')->default('active');
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('student_fee_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->default('success');
        });

        $migration = require base_path('database/migrations/2026_11_11_000000_add_performance_indexes_to_core_tables.php');
        $migration->up();
        // Second call must stay idempotent
        $migration->up();

        $attendanceIndexes = collect(DB::select("PRAGMA index_list('student_attendances')"))->pluck('name')->all();
        $this->assertContains('idx_student_attendances_date', $attendanceIndexes);
        $this->assertContains('idx_student_attendances_date_status', $attendanceIndexes);
        $this->assertContains('idx_student_attendances_class_section_date', $attendanceIndexes);

        $paymentIndexes = collect(DB::select("PRAGMA index_list('payments')"))->pluck('name')->all();
        $this->assertContains('idx_payments_date_student', $paymentIndexes);

        $migration->down();
        $afterDown = collect(DB::select("PRAGMA index_list('student_attendances')"))->pluck('name')->all();
        $this->assertNotContains('idx_student_attendances_date', $afterDown);
    }

    public function test_model_caching_and_automatic_invalidation(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('site_title')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sidebar_menus', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('path')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sidebar_sub_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sidebar_menu_id');
            $table->string('title');
            $table->string('path')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $setting = GeneralSetting::create([
            'school_name' => 'Initial School',
            'site_title' => 'ERP',
        ]);
        $this->assertSame('Initial School', GeneralSetting::cached()->school_name);
        $this->assertTrue(Cache::has(GeneralSetting::CACHE_KEY));

        $setting->update(['school_name' => 'Updated School']);
        $this->assertFalse(Cache::has(GeneralSetting::CACHE_KEY));
        $this->assertSame('Updated School', GeneralSetting::cached()->school_name);

        $ay = AcademicYear::create([
            'name' => '2026-27',
            'is_current' => true,
        ]);
        $this->assertSame($ay->id, AcademicYear::cachedCurrentId());
        $this->assertCount(1, AcademicYear::cachedList());

        $ay->update(['name' => '2026-28']);
        $this->assertFalse(Cache::has(AcademicYear::CACHE_KEY_LIST));
        $this->assertSame('2026-28', AcademicYear::cachedList()->first()->name);

        $menu = SidebarMenu::create([
            'section' => 'Core',
            'title' => 'Dashboard',
            'order' => 1,
            'is_active' => true,
        ]);
        Cache::put('sidebar_menus_all', ['stale'], 300);
        SidebarSubMenu::create([
            'sidebar_menu_id' => $menu->id,
            'title' => 'Overview',
            'is_active' => true,
        ]);
        $this->assertFalse(Cache::has('sidebar_menus_all'));
    }

    public function test_respond_paginated_or_all_preserves_legacy_response_when_unpaginated(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (['2024', '2025', '2026'] as $name) {
            AcademicYear::create(['name' => $name]);
        }

        $controller = new class extends Controller {
            public function expose(Request $request)
            {
                return $this->respondPaginatedOrAll(AcademicYear::query()->orderBy('id'), $request, 2);
            }
        };

        $unpaginated = $controller->expose(Request::create('/api/test', 'GET'))->getData(true);
        $this->assertTrue($unpaginated['success']);
        $this->assertCount(3, $unpaginated['data']);
        $this->assertArrayNotHasKey('meta', $unpaginated);

        $paginated = $controller->expose(Request::create('/api/test?page=1&per_page=2', 'GET'))->getData(true);
        $this->assertTrue($paginated['success']);
        $this->assertCount(2, $paginated['data']);
        $this->assertSame([
            'current_page' => 1,
            'last_page' => 2,
            'per_page' => 2,
            'total' => 3,
        ], $paginated['meta']);
    }

    public function test_fee_assignment_service_batches_items_and_skips_duplicates(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('fee_type_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('fee_structure_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedBigInteger('fee_type_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('fee_structure_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('student_fee_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_fee_id');
            $table->unsignedBigInteger('fee_structure_item_id')->nullable();
            $table->unsignedBigInteger('fee_type_id');
            $table->unsignedBigInteger('fee_installment_id')->nullable();
            $table->string('name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        $s1 = Student::forceCreate(['class_id' => 1, 'academic_year_id' => 1, 'status' => 1]);
        $s2 = Student::forceCreate(['class_id' => 1, 'academic_year_id' => 1, 'status' => 1]);
        $tuition = FeeType::forceCreate(['name' => 'Tuition']);
        $lab = FeeType::forceCreate(['name' => 'Lab']);

        $structure = FeeStructure::forceCreate([
            'name' => 'Annual Fee',
            'class_id' => 1,
            'academic_year_id' => 1,
            'amount' => 1500,
            'total_amount' => 1500,
        ]);
        FeeStructureItem::forceCreate(['fee_structure_id' => $structure->id, 'fee_type_id' => $tuition->id, 'amount' => 1000]);
        FeeStructureItem::forceCreate(['fee_structure_id' => $structure->id, 'fee_type_id' => $lab->id, 'amount' => 500]);

        $service = new FeeAssignmentService();
        $firstRun = $service->assignToStudents(collect([$s1, $s2]), $structure, 1, '2026-06-30', 1);
        $this->assertSame(2, $firstRun['assigned_count']);
        $this->assertSame(0, $firstRun['skipped_count']);
        $this->assertCount(2, $firstRun['student_fee_ids']);
        $this->assertSame(4, StudentFeeItem::count());
        $this->assertSame('1500.00', StudentFee::first()->balance_amount);

        // Second run skips both cleanly
        $secondRun = $service->assignToStudents(collect([$s1, $s2]), $structure, 1, '2026-06-30', 1);
        $this->assertSame(0, $secondRun['assigned_count']);
        $this->assertSame(2, $secondRun['skipped_count']);
    }

    public function test_student_photo_service_blocks_unsafe_paths_and_form_requests_validate(): void
    {
        $this->assertNull(StudentPhotoService::upload(['photo' => '../../evil.php'], 'photo', 'uploads/student_photos'));
        $this->assertSame('uploads/student_photos/sample.jpg', StudentPhotoService::upload(['photo' => 'uploads/student_photos/sample.jpg'], 'photo', 'uploads/student_photos'));

        $examReq = new SaveExamResultRequest();
        $this->assertTrue(Validator::make([], $examReq->rules())->fails());

        $feeReq = new AssignStudentFeeRequest();
        $this->assertTrue(Validator::make([], $feeReq->rules())->fails());

        $settingsReq = new UpdateGeneralSettingsRequest();
        $this->assertTrue(Validator::make([], $settingsReq->rules())->fails());
    }

    public function test_role_user_counts_exclude_soft_deleted_and_removed_users(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        $teacherRoleId = DB::table('roles')->insertGetId([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);

        $activeUserId = DB::table('users')->insertGetId([
            'name' => 'Active Teacher',
            'email' => 'active.teacher@example.com',
            'password' => 'secret',
            'deleted_at' => null,
        ]);
        $softDeletedUserId = DB::table('users')->insertGetId([
            'name' => 'Soft Deleted Teacher',
            'email' => 'soft.teacher@example.com',
            'password' => 'secret',
            'deleted_at' => now(),
        ]);

        // 1 active user, 1 soft-deleted user, 1 hard-deleted ghost user (id 999)
        DB::table('model_has_roles')->insert([
            ['role_id' => $teacherRoleId, 'model_type' => \App\Models\User::class, 'model_id' => $activeUserId],
            ['role_id' => $teacherRoleId, 'model_type' => \App\Models\User::class, 'model_id' => $softDeletedUserId],
            ['role_id' => $teacherRoleId, 'model_type' => \App\Models\User::class, 'model_id' => 999],
        ]);

        $controller = new \App\Http\Controllers\Api\RoleController();
        $response = $controller->index(Request::create('/api/roles', 'GET'))->getData(true);

        $this->assertTrue($response['success']);
        $this->assertSame(1, $response['data'][0]['users_count']);

        $showResponse = $controller->show($teacherRoleId)->getData(true);
        $this->assertSame(1, $showResponse['data']['users_count']);
    }

    public function test_student_attendance_monthly_report_includes_notes(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('admission_no')->nullable();
            $table->string('student_code')->nullable();
            $table->string('roll_no')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->date('date')->nullable();
            $table->string('status')->default('Present');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $classId = DB::table('classes')->insertGetId(['name' => 'Class 1']);
        $sectionId = DB::table('sections')->insertGetId(['name' => 'A']);
        $studentId = DB::table('students')->insertGetId([
            'full_name' => 'TEST 01',
            'admission_no' => '123455',
            'roll_no' => '1',
            'class_id' => $classId,
            'section_id' => $sectionId,
        ]);

        DB::table('student_attendances')->insert([
            [
                'student_id' => $studentId,
                'date' => '2026-11-10',
                'status' => 'Present',
                'note' => null,
            ],
            [
                'student_id' => $studentId,
                'date' => '2026-11-12',
                'status' => 'Leave',
                'note' => 'Medical leave',
            ],
        ]);

        $controller = new \App\Http\Controllers\Api\StudentAttendanceController();
        $res = $controller->monthlyReport(
            Request::create('/api/student-attendances/monthly-report?month=11&year=2026', 'GET')
        )->getData(true);

        $this->assertTrue($res['success']);
        $row = $res['data'][0];
        $this->assertSame('P', $row['days'][10]);
        $this->assertSame('', $row['notes'][10]);
        $this->assertSame('Le', $row['days'][12]);
        $this->assertSame('Medical leave', $row['notes'][12]);
        $this->assertSame('', $row['notes'][1]);
    }

    public function test_academic_year_sidebar_submenu_migration(): void
    {
        Schema::create('sidebar_menus', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('title', 150);
            $table->string('icon', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sidebar_sub_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sidebar_menu_id');
            $table->string('title', 150);
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $menu = SidebarMenu::create([
            'section' => 'Settings Section',
            'title' => 'Settings Section',
            'path' => '/settings-section',
            'order' => 18,
            'is_active' => true,
        ]);

        SidebarSubMenu::create([
            'sidebar_menu_id' => $menu->id,
            'title' => 'General Settings',
            'order' => 1,
            'is_active' => true,
        ]);
        SidebarSubMenu::create([
            'sidebar_menu_id' => $menu->id,
            'title' => 'Users & Access',
            'order' => 2,
            'is_active' => true,
        ]);

        $migration = require base_path('database/migrations/2026_11_13_000000_add_academic_year_to_sidebar_sub_menus.php');
        $migration->up();
        // Check idempotency
        $migration->up();

        $subTitles = SidebarSubMenu::where('sidebar_menu_id', $menu->id)->orderBy('order')->pluck('title')->all();
        $this->assertSame(['General Settings', 'Academic Year', 'Users & Access'], $subTitles);

        $migration->down();
        $subTitlesAfter = SidebarSubMenu::where('sidebar_menu_id', $menu->id)->orderBy('order')->pluck('title')->all();
        $this->assertSame(['General Settings', 'Users & Access'], $subTitlesAfter);
    }

    public function test_sidebar_menus_and_submenus_permission_mapping(): void
    {
        Schema::create('sidebar_menus', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('title', 150);
            $table->string('icon', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sidebar_sub_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sidebar_menu_id');
            $table->string('title', 150);
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        $menu = SidebarMenu::create([
            'section' => 'Student',
            'title' => 'Student Info',
            'path' => '/student-info',
            'order' => 1,
            'is_active' => true,
        ]);
        $sub = SidebarSubMenu::create([
            'sidebar_menu_id' => $menu->id,
            'title' => 'Student Category',
            'order' => 1,
            'is_active' => true,
        ]);

        $settingsMenu = SidebarMenu::create([
            'section' => 'Settings Section',
            'title' => 'Settings Section',
            'path' => '/settings-section',
            'order' => 2,
            'is_active' => true,
        ]);
        $academicSub = SidebarSubMenu::create([
            'sidebar_menu_id' => $settingsMenu->id,
            'title' => 'Academic Year',
            'order' => 1,
            'is_active' => true,
        ]);

        $migration = require base_path('database/migrations/2026_11_13_020000_populate_dynamic_sidebar_menus_and_permissions.php');
        $migration->up();
        // Test idempotency
        $migration->up();

        $this->assertSame('manage_student_categories', $sub->fresh()->permission);
        $this->assertSame('view_students', $menu->fresh()->permission);
        $this->assertSame('manage_academic_years', $academicSub->fresh()->permission);
        $this->assertSame('manage_general_settings', $settingsMenu->fresh()->permission);

        $controller = new \App\Http\Controllers\Api\SidebarMenuController();
        $response = $controller->index(Request::create('/api/sidebar-menus', 'GET'))->getData(true);

        $this->assertTrue($response['success']);
        $studentSection = collect($response['data'])->firstWhere('title', 'Student');
        $this->assertNotNull($studentSection);
        $studentMenu = $studentSection['menus'][0];
        $this->assertSame('view_students', $studentMenu['permission']);
        $this->assertSame('manage_student_categories', $studentMenu['sub_menus'][0]['permission']);
    }
}

