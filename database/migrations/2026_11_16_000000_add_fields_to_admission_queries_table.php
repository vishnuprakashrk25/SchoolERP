<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds address, description, date, next_follow_up_date, assigned, reference, class_id, no_of_child
     * to admission_queries to match the frontend Add Admission Query form.
     */
    public function up(): void
    {
        Schema::table('admission_queries', function (Blueprint $table) {
            if (!Schema::hasColumn('admission_queries', 'address')) {
                $table->text('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('admission_queries', 'description')) {
                $table->text('description')->nullable()->after('address');
            }
            if (!Schema::hasColumn('admission_queries', 'date')) {
                $table->date('date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('admission_queries', 'next_follow_up_date')) {
                $table->date('next_follow_up_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('admission_queries', 'assigned')) {
                $table->string('assigned')->nullable()->after('next_follow_up_date');
            }
            if (!Schema::hasColumn('admission_queries', 'reference')) {
                $table->string('reference')->nullable()->after('assigned');
            }
            if (!Schema::hasColumn('admission_queries', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('source');
            }
            if (!Schema::hasColumn('admission_queries', 'no_of_child')) {
                $table->integer('no_of_child')->nullable()->default(1)->after('class_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_queries', function (Blueprint $table) {
            $columns = [
                'address',
                'description',
                'date',
                'next_follow_up_date',
                'assigned',
                'reference',
                'class_id',
                'no_of_child',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('admission_queries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
