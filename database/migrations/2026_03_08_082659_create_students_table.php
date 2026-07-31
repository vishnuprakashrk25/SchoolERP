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
        Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('admission_no')->unique();
    $table->string('first_name');
    $table->string('last_name')->nullable();
    $table->date('dob')->nullable();
    $table->string('gender');
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->foreignId('category_id')->nullable()->constrained('student_categories');
    $table->string('photo')->nullable();
    $table->boolean('status')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
