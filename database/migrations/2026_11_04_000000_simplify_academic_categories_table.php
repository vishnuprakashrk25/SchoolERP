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
        Schema::table('academic_categories', function (Blueprint $table) {
            $extraColumns = [
                'class_ids',
                'class_pattern',
                'stream',
                'min_class_level',
                'max_class_level',
                'is_auto_assign',
            ];

            foreach ($extraColumns as $col) {
                if (Schema::hasColumn('academic_categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_categories', 'class_ids')) {
                $table->text('class_ids')->nullable();
                $table->string('class_pattern')->nullable();
                $table->string('stream')->nullable();
                $table->integer('min_class_level')->nullable();
                $table->integer('max_class_level')->nullable();
                $table->boolean('is_auto_assign')->default(true);
            }
        });
    }
};
