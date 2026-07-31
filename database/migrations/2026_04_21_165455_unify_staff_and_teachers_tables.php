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
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        // Drop foreign keys relating to teachers
        try {
            Schema::table('class_teachers', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('class_subjects', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('class_routines', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
            });
        } catch (\Exception $e) {}

        Schema::dropIfExists('teachers');

        // Reconnect them to staff
        Schema::table('class_teachers', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('staff')->onDelete('cascade');
        });

        Schema::table('class_subjects', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('staff')->onDelete('cascade');
        });

        Schema::table('class_routines', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the teachers table
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('designation')->nullable();
            $table->string('qualification')->nullable();
            $table->date('joining_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('class_teachers', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });

        Schema::table('class_subjects', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
