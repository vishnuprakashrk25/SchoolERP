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
        Schema::create('exam_schedules', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('exam_id')->nullable();
    $table->unsignedBigInteger('class_id')->nullable();
    $table->unsignedBigInteger('subject_id')->nullable();
    $table->date('exam_date')->nullable();
    $table->timestamps();
    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};