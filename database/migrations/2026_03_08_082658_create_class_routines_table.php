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
       Schema::create('class_routines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained();
    $table->foreignId('section_id')->constrained();
    $table->foreignId('subject_id')->constrained();
    $table->foreignId('teacher_id')->constrained();
    $table->foreignId('room_id')->constrained('class_rooms');
    $table->string('day');
    $table->time('start_time');
    $table->time('end_time');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_routines');
    }
};
