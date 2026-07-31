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
        Schema::create('student_multi_classes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
    $table->unsignedBigInteger('class_id')->nullable();
    $table->unsignedBigInteger('section_id')->nullable();
    $table->timestamps();
    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_multi_classes');
    }
};