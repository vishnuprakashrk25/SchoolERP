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
        // 1. Upgrade fee_groups
        if (Schema::hasTable('fee_groups')) {
            Schema::table('fee_groups', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_groups', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('academic_id');
                }
                if (!Schema::hasColumn('fee_groups', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('school_id');
                }
                if (!Schema::hasColumn('fee_groups', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('description');
                }
            });
        }

        // 2. Upgrade fee_types
        if (Schema::hasTable('fee_types')) {
            Schema::table('fee_types', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_types', 'fee_group_id')) {
                    $table->unsignedBigInteger('fee_group_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('fee_types', 'code')) {
                    $table->string('code')->nullable()->after('name');
                }
                if (!Schema::hasColumn('fee_types', 'description')) {
                    $table->text('description')->nullable()->after('amount');
                }
                if (!Schema::hasColumn('fee_types', 'billing_cycle')) {
                    $table->string('billing_cycle')->default('one_time')->after('description'); // one_time, monthly, quarterly, half_yearly, annual
                }
                if (!Schema::hasColumn('fee_types', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('billing_cycle');
                }
                if (!Schema::hasColumn('fee_types', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('school_id');
                }
                if (!Schema::hasColumn('fee_types', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('academic_year_id');
                }
                if (!Schema::hasColumn('fee_types', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
            });
        }

        // 3. Upgrade fee_structures
        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_structures', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
                if (!Schema::hasColumn('fee_structures', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('name');
                }
                if (!Schema::hasColumn('fee_structures', 'section_id')) {
                    $table->unsignedBigInteger('section_id')->nullable()->after('class_id');
                }
                if (!Schema::hasColumn('fee_structures', 'category_id')) {
                    $table->unsignedBigInteger('category_id')->nullable()->after('section_id');
                }
                if (!Schema::hasColumn('fee_structures', 'academic_category_id')) {
                    $table->unsignedBigInteger('academic_category_id')->nullable()->after('category_id');
                }
                if (!Schema::hasColumn('fee_structures', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00)->after('amount');
                }
                if (!Schema::hasColumn('fee_structures', 'description')) {
                    $table->text('description')->nullable()->after('total_amount');
                }
                if (!Schema::hasColumn('fee_structures', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('description');
                }
                if (!Schema::hasColumn('fee_structures', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('school_id');
                }
                if (!Schema::hasColumn('fee_structures', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
                if (!Schema::hasColumn('fee_structures', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('updated_by');
                }
            });
        }

        // 4. Create fee_structure_items
        if (!Schema::hasTable('fee_structure_items')) {
            Schema::create('fee_structure_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fee_structure_id');
                $table->unsignedBigInteger('fee_type_id');
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->date('due_date')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('fee_structure_id');
                $table->index('fee_type_id');
            });
        }

        // 5. Create fee_installments
        if (!Schema::hasTable('fee_installments')) {
            Schema::create('fee_installments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fee_structure_id')->nullable();
                $table->string('name');
                $table->date('due_date');
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->string('late_fee_type')->default('fixed'); // fixed, percentage, daily
                $table->decimal('late_fee_amount', 10, 2)->default(0.00);
                $table->integer('grace_period_days')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index('fee_structure_id');
                $table->index('due_date');
            });
        }

        // 6. Upgrade student_fees
        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                if (!Schema::hasColumn('student_fees', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('student_id');
                }
                if (!Schema::hasColumn('student_fees', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0.00)->after('paid_amount');
                }
                if (!Schema::hasColumn('student_fees', 'fine_amount')) {
                    $table->decimal('fine_amount', 12, 2)->default(0.00)->after('discount_amount');
                }
                if (!Schema::hasColumn('student_fees', 'balance_amount')) {
                    $table->decimal('balance_amount', 12, 2)->default(0.00)->after('fine_amount');
                }
                if (!Schema::hasColumn('student_fees', 'remarks')) {
                    $table->text('remarks')->nullable()->after('balance_amount');
                }
                if (!Schema::hasColumn('student_fees', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
                }
                if (!Schema::hasColumn('student_fees', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
            });
        }

        // 7. Create student_fee_items
        if (!Schema::hasTable('student_fee_items')) {
            Schema::create('student_fee_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_fee_id');
                $table->unsignedBigInteger('fee_structure_item_id')->nullable();
                $table->unsignedBigInteger('fee_type_id');
                $table->unsignedBigInteger('fee_installment_id')->nullable();
                $table->string('name');
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->decimal('paid_amount', 12, 2)->default(0.00);
                $table->decimal('discount_amount', 12, 2)->default(0.00);
                $table->decimal('fine_amount', 12, 2)->default(0.00);
                $table->decimal('balance_amount', 12, 2)->default(0.00);
                $table->date('due_date')->nullable();
                $table->string('status')->default('pending'); // pending, partial, paid, waived
                $table->timestamps();
                $table->softDeletes();

                $table->index('student_fee_id');
                $table->index('fee_type_id');
                $table->index('fee_installment_id');
                $table->index('status');
            });
        }

        // 8. Upgrade payments
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'payment_no')) {
                    $table->string('payment_no')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('payments', 'receipt_no')) {
                    $table->string('receipt_no')->nullable()->unique()->after('payment_no');
                }
                if (!Schema::hasColumn('payments', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable()->after('receipt_no');
                }
                if (!Schema::hasColumn('payments', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('student_id');
                }
                if (!Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method')->default('cash')->after('payment_date'); // cash, upi, bank_transfer, cheque, card, neft_rtgs, online_gateway, other
                }
                if (!Schema::hasColumn('payments', 'payment_reference')) {
                    $table->string('payment_reference')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('payments', 'bank_name')) {
                    $table->string('bank_name')->nullable()->after('payment_reference');
                }
                if (!Schema::hasColumn('payments', 'cheque_date')) {
                    $table->date('cheque_date')->nullable()->after('bank_name');
                }
                if (!Schema::hasColumn('payments', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0.00)->after('amount');
                }
                if (!Schema::hasColumn('payments', 'fine_amount')) {
                    $table->decimal('fine_amount', 12, 2)->default(0.00)->after('discount_amount');
                }
                if (!Schema::hasColumn('payments', 'total_paid')) {
                    $table->decimal('total_paid', 12, 2)->default(0.00)->after('fine_amount');
                }
                if (!Schema::hasColumn('payments', 'note')) {
                    $table->text('note')->nullable()->after('total_paid');
                }
                if (!Schema::hasColumn('payments', 'collected_by')) {
                    $table->unsignedBigInteger('collected_by')->nullable()->after('note');
                }
                if (!Schema::hasColumn('payments', 'status')) {
                    $table->string('status')->default('success')->after('collected_by'); // success, reversed, cancelled
                }
                if (!Schema::hasColumn('payments', 'reversal_reason')) {
                    $table->text('reversal_reason')->nullable()->after('status');
                }
                if (!Schema::hasColumn('payments', 'reversed_at')) {
                    $table->dateTime('reversed_at')->nullable()->after('reversal_reason');
                }
                if (!Schema::hasColumn('payments', 'reversed_by')) {
                    $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
                }
            });
        }

        // 9. Create fee_payment_items
        if (!Schema::hasTable('fee_payment_items')) {
            Schema::create('fee_payment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_id');
                $table->unsignedBigInteger('student_fee_item_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->decimal('discount_amount', 12, 2)->default(0.00);
                $table->decimal('fine_amount', 12, 2)->default(0.00);
                $table->string('note')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('payment_id');
                $table->index('student_fee_item_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payment_items');
        Schema::dropIfExists('student_fee_items');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('fee_structure_items');

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $cols = ['payment_no', 'receipt_no', 'student_id', 'academic_year_id', 'payment_method', 'payment_reference', 'bank_name', 'cheque_date', 'discount_amount', 'fine_amount', 'total_paid', 'note', 'collected_by', 'status', 'reversal_reason', 'reversed_at', 'reversed_by'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('payments', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $cols = ['academic_year_id', 'discount_amount', 'fine_amount', 'balance_amount', 'remarks', 'created_by', 'updated_by'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('student_fees', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                $cols = ['name', 'academic_year_id', 'section_id', 'category_id', 'academic_category_id', 'total_amount', 'description', 'school_id', 'created_by', 'updated_by', 'is_active'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('fee_structures', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('fee_types')) {
            Schema::table('fee_types', function (Blueprint $table) {
                $cols = ['fee_group_id', 'code', 'description', 'billing_cycle', 'school_id', 'academic_year_id', 'created_by', 'updated_by'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('fee_types', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('fee_groups')) {
            Schema::table('fee_groups', function (Blueprint $table) {
                $cols = ['academic_year_id', 'branch_id', 'is_active'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('fee_groups', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
