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
        Schema::create('timetables', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('class_id')->nullable();
    $table->unsignedBigInteger('section_id')->nullable();
    $table->unsignedBigInteger('subject_id')->nullable();
    $table->unsignedBigInteger('teacher_id')->nullable();
    $table->string('day')->nullable();
    $table->string('start_time')->nullable();
    $table->string('end_time')->nullable();
    $table->timestamps();
    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};