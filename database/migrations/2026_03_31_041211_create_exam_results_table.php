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
        Schema::create('exam_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
    $table->unsignedBigInteger('exam_id')->nullable();
    $table->unsignedBigInteger('subject_id')->nullable();
    $table->string('marks')->nullable();
    $table->timestamps();
    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};