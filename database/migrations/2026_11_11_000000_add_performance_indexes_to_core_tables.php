<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance indexes for high-frequency queries across attendances, fees, payments, exams, and parents.
     */
    private array $indexes = [
        'student_attendances' => [
            'idx_student_attendances_date' => ['date'],
            'idx_student_attendances_student_date' => ['student_id', 'date'],
            'idx_student_attendances_date_status' => ['date', 'status'],
            'idx_student_attendances_class_section_date' => ['class_id', 'section_id', 'date'],
        ],
        'staff_attendances' => [
            'idx_staff_attendances_date' => ['date'],
            'idx_staff_attendances_staff_date' => ['staff_id', 'date'],
            'idx_staff_attendances_date_status' => ['date', 'status'],
        ],
        'student_parents' => [
            'idx_student_parents_father_phone' => ['father_phone'],
            'idx_student_parents_mother_phone' => ['mother_phone'],
            'idx_student_parents_guardian_phone' => ['guardian_phone'],
        ],
        'payments' => [
            'idx_payments_student_id' => ['student_id'],
            'idx_payments_student_fee_id' => ['student_fee_id'],
            'idx_payments_academic_year_id' => ['academic_year_id'],
            'idx_payments_payment_date' => ['payment_date'],
            'idx_payments_date_student' => ['payment_date', 'student_id'],
            'idx_payments_status' => ['status'],
        ],
        'student_fees' => [
            'idx_student_fees_student_structure_year' => ['student_id', 'fee_structure_id', 'academic_year_id'],
            'idx_student_fees_academic_year_id' => ['academic_year_id'],
            'idx_student_fees_status' => ['status'],
        ],
        'exam_results' => [
            'idx_exam_results_exam_subject' => ['exam_id', 'subject_id'],
            'idx_exam_results_student_exam' => ['student_id', 'exam_id'],
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->indexes as $table => $tableIndexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($tableIndexes as $indexName => $columns) {
                $allColumnsExist = collect($columns)->every(fn ($col) => Schema::hasColumn($table, $col));
                if (!$allColumnsExist || $this->hasIndex($table, $indexName)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
                    $blueprint->index($columns, $indexName);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->indexes as $table => $tableIndexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($tableIndexes as $indexName => $columns) {
                if (!$this->hasIndex($table, $indexName)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                    $blueprint->dropIndex($indexName);
                });
            }
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($rows);
        }

        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('{$table}')");
            foreach ($rows as $row) {
                if (($row->name ?? '') === $indexName) {
                    return true;
                }
            }
        }

        return false;
    }
};

