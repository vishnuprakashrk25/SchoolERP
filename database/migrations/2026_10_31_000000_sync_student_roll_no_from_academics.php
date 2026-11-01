<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sync roll_no from student_academics to students table where roll_no in students is empty or null
        try {
            DB::statement("
                UPDATE students s
                INNER JOIN (
                    SELECT student_id, roll_no
                    FROM student_academics
                    WHERE roll_no IS NOT NULL AND roll_no != ''
                    ORDER BY id DESC
                ) sa ON sa.student_id = s.id
                SET s.roll_no = sa.roll_no
                WHERE (s.roll_no IS NULL OR s.roll_no = '')
            ");
        } catch (\Throwable $e) {
            // Fallback for any DBMS differences
            $academics = DB::table('student_academics')
                ->whereNotNull('roll_no')
                ->where('roll_no', '!=', '')
                ->orderBy('id', 'desc')
                ->get();

            foreach ($academics as $row) {
                DB::table('students')
                    ->where('id', $row->student_id)
                    ->where(function ($q) {
                        $q->whereNull('roll_no')->orWhere('roll_no', '');
                    })
                    ->update(['roll_no' => $row->roll_no]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback needed
    }
};
