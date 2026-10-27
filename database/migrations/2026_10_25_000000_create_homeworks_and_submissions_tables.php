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
        // 1. Homeworks table
        if (!Schema::hasTable('homeworks')) {
            Schema::create('homeworks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->index();
                $table->unsignedBigInteger('subject_id')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('evaluated_by')->nullable()->index();
                $table->date('homework_date');
                $table->date('submission_date');
                $table->date('evaluation_date')->nullable();
                $table->decimal('marks', 8, 2)->default(100);
                $table->text('description')->nullable();
                $table->string('file')->nullable();
                $table->string('status', 50)->default('active'); // active, evaluated, completed, cancelled
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Homework Submissions / Evaluations table
        if (!Schema::hasTable('homework_submissions')) {
            Schema::create('homework_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('homework_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->dateTime('submission_date')->nullable();
                $table->string('submission_file')->nullable();
                $table->text('student_notes')->nullable();
                $table->decimal('marks', 8, 2)->nullable();
                $table->string('status', 50)->default('pending'); // pending, submitted, completed, evaluated
                $table->unsignedBigInteger('evaluated_by')->nullable()->index();
                $table->date('evaluation_date')->nullable();
                $table->text('description')->nullable(); // Teacher feedback notes
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['homework_id', 'student_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homeworks');
    }
};
