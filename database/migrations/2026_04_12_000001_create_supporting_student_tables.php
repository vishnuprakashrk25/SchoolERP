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
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fare')->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no');
            $table->string('vehicle_model')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_license')->nullable();
            $table->string('driver_contact')->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // e.g. Boys, Girls
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dormitory_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dormitory_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('number_of_bed')->nullable();
            $table->string('cost_per_bed')->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dormitory_rooms');
        Schema::dropIfExists('dormitories');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('student_groups');
    }
};
