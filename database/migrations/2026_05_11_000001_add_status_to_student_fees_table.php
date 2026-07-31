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
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'status')) {
                $table->string('status')->default('pending')->after('amount');
            }
            if (!Schema::hasColumn('student_fees', 'due_date')) {
                $table->date('due_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('student_fees', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('due_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn(['status', 'due_date', 'paid_amount']);
        });
    }
};
