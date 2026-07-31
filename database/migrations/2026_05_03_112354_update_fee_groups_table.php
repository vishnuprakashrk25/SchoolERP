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
        Schema::table('fee_groups', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->unsignedBigInteger('school_id')->nullable()->after('description');
            $table->unsignedBigInteger('academic_id')->nullable()->after('school_id');
            $table->unsignedBigInteger('created_by')->nullable()->after('academic_id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_groups', function (Blueprint $table) {
            $table->dropColumn(['description', 'school_id', 'academic_id', 'created_by', 'updated_by']);
        });
    }
};
