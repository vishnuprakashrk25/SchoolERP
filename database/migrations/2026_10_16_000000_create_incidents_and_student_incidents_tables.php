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
        if (!Schema::hasTable('incidents')) {
            Schema::create('incidents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('title');
                $table->integer('point')->default(0);
                $table->boolean('is_negative')->default(false);
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('student_incidents')) {
            Schema::create('student_incidents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('incident_id')->index();
                $table->string('title')->nullable();
                $table->integer('point')->default(0);
                $table->boolean('is_negative')->default(false);
                $table->date('date')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('assigned_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_incidents');
        Schema::dropIfExists('incidents');
    }
};
