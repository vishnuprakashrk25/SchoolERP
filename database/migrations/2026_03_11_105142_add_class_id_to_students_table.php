<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {

            if (!Schema::hasColumn('students', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('students', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable();
                $table->foreign('class_id')
                    ->references('id')
                    ->on('classes')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('students', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable();
                $table->foreign('section_id')
                    ->references('id')
                    ->on('sections')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }

            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender',['male','female','other'])->nullable();
            }

            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            $table->dropForeign(['class_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'user_id',
                'class_id',
                'section_id',
               
                'date_of_birth',
                'gender',
                'address'
            ]);

        });
    }
};