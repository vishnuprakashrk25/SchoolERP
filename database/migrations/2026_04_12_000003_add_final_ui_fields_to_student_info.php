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
        Schema::create('student_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('relation')->nullable();
            $table->string('phone');
            $table->timestamps();
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->string('guardian_is')->nullable()->after('guardian_photo')->comment('Father, Mother, Other');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('previous_school_name')->nullable()->after('birth_certificate_no');
            $table->string('previous_qualification')->nullable()->after('previous_school_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['previous_school_name', 'previous_qualification']);
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->dropColumn('guardian_is');
        });

        Schema::dropIfExists('student_emergency_contacts');
    }
};
