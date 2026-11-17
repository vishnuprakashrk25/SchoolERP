<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Makes academic_year_id nullable in classes table.
     */
    public function up(): void
    {
        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table) {
                if (Schema::hasColumn('classes', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table) {
                if (Schema::hasColumn('classes', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable(false)->change();
                }
            });
        }
    }
};
