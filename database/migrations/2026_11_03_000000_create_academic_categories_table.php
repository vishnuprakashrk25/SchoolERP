<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create academic_categories table
        if (!Schema::hasTable('academic_categories')) {
            Schema::create('academic_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->text('class_ids')->nullable();
                $table->string('class_pattern')->nullable();
                $table->string('stream')->nullable();
                $table->integer('min_class_level')->nullable();
                $table->integer('max_class_level')->nullable();
                $table->boolean('is_auto_assign')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Add academic_category_id to classes table
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'academic_category_id')) {
                $table->foreignId('academic_category_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('academic_categories')
                    ->nullOnDelete();
            }
        });

        // 3. Add academic_category_id to students table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'academic_category_id')) {
                $table->foreignId('academic_category_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('academic_categories')
                    ->nullOnDelete();
            }
        });

        // 4. Migrate academic records from student_categories into academic_categories
        if (Schema::hasTable('student_categories')) {
            $academicRecords = DB::table('student_categories')
                ->whereIn('id', [6, 7, 8, 9, 10, 11])
                ->orWhere('name', 'like', 'Rainbow%')
                ->orWhere('name', 'like', 'Planet%')
                ->orWhere('name', 'like', 'Galaxy%')
                ->get();

            foreach ($academicRecords as $rec) {
                DB::table('academic_categories')->updateOrInsert(
                    ['id' => $rec->id],
                    [
                        'name' => $rec->name,
                        'description' => $rec->description ?? null,
                        'class_ids' => $rec->class_ids ?? null,
                        'class_pattern' => $rec->class_pattern ?? null,
                        'stream' => $rec->stream ?? null,
                        'min_class_level' => $rec->min_class_level ?? null,
                        'max_class_level' => $rec->max_class_level ?? null,
                        'is_auto_assign' => $rec->is_auto_assign ?? 1,
                        'created_at' => $rec->created_at ?? now(),
                        'updated_at' => $rec->updated_at ?? now(),
                        'deleted_at' => null,
                    ]
                );
            }

            // Sync classes academic_category_id from category_id
            DB::table('classes')
                ->whereIn('category_id', [6, 7, 8, 9, 10, 11])
                ->update(['academic_category_id' => DB::raw('category_id')]);

            // Sync students academic_category_id from category_id where category_id is an academic category
            DB::table('students')
                ->whereIn('category_id', [6, 7, 8, 9, 10, 11])
                ->update([
                    'academic_category_id' => DB::raw('category_id'),
                    'category_id' => null, // Clear incorrect academic category from student demographic category
                ]);

            // Soft delete academic records from student_categories table so GET /api/student-categories only returns demographic categories
            DB::table('student_categories')
                ->whereIn('id', [6, 7, 8, 9, 10, 11])
                ->orWhere('name', 'like', 'Rainbow%')
                ->orWhere('name', 'like', 'Planet%')
                ->orWhere('name', 'like', 'Galaxy%')
                ->update(['deleted_at' => now()]);

            // Ensure student demographic categories exist (General, OBC, SC, ST, Other)
            $socialCategories = [
                ['id' => 2, 'name' => 'General', 'description' => 'General Category'],
                ['id' => 3, 'name' => 'OBC', 'description' => 'Other Backward Class'],
                ['id' => 4, 'name' => 'SC', 'description' => 'Scheduled Caste'],
                ['id' => 5, 'name' => 'ST', 'description' => 'Scheduled Tribe'],
                ['name' => 'Other', 'description' => 'Other Category'],
            ];

            foreach ($socialCategories as $cat) {
                $query = DB::table('student_categories')->where('name', $cat['name']);
                if (isset($cat['id'])) {
                    $query->orWhere('id', $cat['id']);
                }
                $existing = $query->first();

                if ($existing) {
                    DB::table('student_categories')->where('id', $existing->id)->update([
                        'name' => $cat['name'],
                        'description' => $cat['description'],
                        'deleted_at' => null,
                    ]);
                } else {
                    DB::table('student_categories')->insert([
                        'name' => $cat['name'],
                        'description' => $cat['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'academic_category_id')) {
                $table->dropForeign(['academic_category_id']);
                $table->dropColumn('academic_category_id');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'academic_category_id')) {
                $table->dropForeign(['academic_category_id']);
                $table->dropColumn('academic_category_id');
            }
        });

        Schema::dropIfExists('academic_categories');
    }
};
