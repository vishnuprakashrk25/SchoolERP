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
        Schema::create('student_fees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
    $table->unsignedBigInteger('fee_structure_id')->nullable();
    $table->decimal('amount', 10, 2)->default(0);
    $table->timestamps();
    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};