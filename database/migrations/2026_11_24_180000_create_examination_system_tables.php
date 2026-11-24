<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Exam Types
        if (!Schema::hasTable('exam_types')) {
            Schema::create('exam_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_group')->default(false); // Group / Term Exam Status toggle
                $table->boolean('status')->default(true);
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Ensure columns in existing exams table if needed
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'is_group')) {
                    $table->boolean('is_group')->default(false)->after('name');
                }
                if (!Schema::hasColumn('exams', 'status')) {
                    $table->boolean('status')->default(true)->after('is_group');
                }
                if (!Schema::hasColumn('exams', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('academic_year_id')->index();
                }
            });
        }

        // 2. Exam Setups (with Mark Distributions & Pass Mark Criteria)
        if (!Schema::hasTable('exam_setups')) {
            Schema::create('exam_setups', function (Blueprint $table) {
                $table->id();
                $table->string('exam_title');
                $table->unsignedBigInteger('exam_type_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index();
                $table->decimal('pass_mark', 8, 2)->default(33.00);
                $table->decimal('total_mark', 8, 2)->default(100.00);
                $table->json('mark_distributions')->nullable(); // [{"title": "Written", "mark": 50}, ...]
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Upgrade Exam Schedules
        if (!Schema::hasTable('exam_schedules')) {
            Schema::create('exam_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->nullable()->index();
                $table->unsignedBigInteger('exam_type_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->nullable()->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index();
                $table->date('exam_date')->nullable();
                $table->string('start_time')->nullable();
                $table->string('end_time')->nullable();
                $table->string('duration')->nullable(); // e.g. "45 Minutes"
                $table->string('room')->nullable(); // e.g. "235432"
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('exam_schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_schedules', 'exam_type_id')) {
                    $table->unsignedBigInteger('exam_type_id')->nullable()->after('exam_id')->index();
                }
                if (!Schema::hasColumn('exam_schedules', 'section_id')) {
                    $table->unsignedBigInteger('section_id')->nullable()->after('class_id')->index();
                }
                if (!Schema::hasColumn('exam_schedules', 'teacher_id')) {
                    $table->unsignedBigInteger('teacher_id')->nullable()->after('subject_id')->index();
                }
                if (!Schema::hasColumn('exam_schedules', 'start_time')) {
                    $table->string('start_time')->nullable()->after('exam_date');
                }
                if (!Schema::hasColumn('exam_schedules', 'end_time')) {
                    $table->string('end_time')->nullable()->after('start_time');
                }
                if (!Schema::hasColumn('exam_schedules', 'duration')) {
                    $table->string('duration')->nullable()->after('end_time');
                }
                if (!Schema::hasColumn('exam_schedules', 'room')) {
                    $table->string('room')->nullable()->after('duration');
                }
                if (!Schema::hasColumn('exam_schedules', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('room')->index();
                }
                if (!Schema::hasColumn('exam_schedules', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('academic_year_id')->index();
                }
            });
        }

        // 4. Exam Attendances
        if (!Schema::hasTable('exam_attendances')) {
            Schema::create('exam_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->string('status', 10)->default('P'); // P: Present, A: Absent, L: Late
                $table->string('note')->nullable();
                $table->date('date')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 5. Exam Marks / Mark Register
        if (!Schema::hasTable('exam_marks')) {
            Schema::create('exam_marks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->nullable()->index();
                $table->unsignedBigInteger('exam_setup_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->json('marks_distribution')->nullable(); // {"Written": 40, "Practical": 20}
                $table->decimal('total_marks', 8, 2)->default(0.00);
                $table->string('grade', 10)->nullable(); // A+, A, B, etc.
                $table->decimal('gpa', 4, 2)->nullable(); // 5.0, 4.0, etc.
                $table->boolean('is_absent')->default(false);
                $table->string('remarks')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 6. Mark Grades
        if (!Schema::hasTable('mark_grades')) {
            Schema::create('mark_grades', function (Blueprint $table) {
                $table->id();
                $table->string('grade_name'); // e.g. "A+"
                $table->decimal('gpa', 4, 2)->default(0.00); // e.g. 5.00
                $table->decimal('percent_from', 5, 2)->default(0.00); // e.g. 80.00
                $table->decimal('percent_to', 5, 2)->default(100.00); // e.g. 100.00
                $table->decimal('gpa_from', 4, 2)->default(0.00); // e.g. 5.00
                $table->decimal('gpa_to', 4, 2)->default(5.99); // e.g. 5.99
                $table->text('description')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mark_grades');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_attendances');
        Schema::dropIfExists('exam_setups');
        Schema::dropIfExists('exam_types');
    }
};
