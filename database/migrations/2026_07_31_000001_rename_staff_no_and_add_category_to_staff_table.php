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
        if (Schema::hasColumn('staff', 'staff_no') && !Schema::hasColumn('staff', 'staff_code')) {
            DB::statement("ALTER TABLE `staff` CHANGE COLUMN `staff_no` `staff_code` VARCHAR(255) NULL");
        } elseif (!Schema::hasColumn('staff', 'staff_code')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('staff_code')->nullable()->after('designation');
            });
        }

        if (!Schema::hasColumn('staff', 'category')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('category')->nullable()->after('designation');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('staff', 'staff_code') && !Schema::hasColumn('staff', 'staff_no')) {
            DB::statement("ALTER TABLE `staff` CHANGE COLUMN `staff_code` `staff_no` VARCHAR(255) NULL");
        }

        if (Schema::hasColumn('staff', 'category')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
