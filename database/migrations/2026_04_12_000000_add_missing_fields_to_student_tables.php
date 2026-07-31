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
        Schema::table('students', function (Blueprint $table) {
            $table->date('admission_date')->nullable()->after('admission_no');
            $table->string('roll_no')->nullable()->after('admission_date');
            $table->string('religion')->nullable()->after('dob');
            $table->string('caste')->nullable()->after('religion');
            $table->string('blood_group')->nullable()->after('caste');
            $table->string('height')->nullable()->after('blood_group');
            $table->string('weight')->nullable()->after('height');
            $table->date('as_on_date')->nullable()->after('weight');
            $table->string('bank_account_no')->nullable()->after('permanent_address');
            $table->string('bank_name')->nullable()->after('bank_account_no');
            $table->string('ifsc_code')->nullable()->after('bank_name');
            $table->string('national_id_no')->nullable()->after('ifsc_code');
            $table->string('local_id_no')->nullable()->after('national_id_no');
            $table->string('birth_certificate_no')->nullable()->after('local_id_no');
            $table->text('previous_school_details')->nullable()->after('birth_certificate_no');
            $table->text('note')->nullable()->after('previous_school_details');
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->string('father_occupation')->nullable()->after('father_phone');
            $table->string('father_photo')->nullable()->after('father_occupation');
            $table->string('mother_occupation')->nullable()->after('mother_phone');
            $table->string('mother_photo')->nullable()->after('mother_occupation');
            $table->string('guardian_occupation')->nullable()->after('guardian_phone');
            $table->string('guardian_email')->nullable()->after('guardian_occupation');
            $table->string('guardian_relation')->nullable()->after('guardian_email');
            $table->text('guardian_address')->nullable()->after('guardian_relation');
            $table->string('guardian_photo')->nullable()->after('guardian_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'admission_date', 'roll_no', 'religion', 'caste', 'blood_group',
                'height', 'weight', 'as_on_date', 'bank_account_no', 'bank_name',
                'ifsc_code', 'national_id_no', 'local_id_no', 'birth_certificate_no',
                'previous_school_details', 'note'
            ]);
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->dropColumn([
                'father_occupation', 'father_photo', 'mother_occupation', 'mother_photo',
                'guardian_occupation', 'guardian_email', 'guardian_relation',
                'guardian_address', 'guardian_photo'
            ]);
        });
    }
};
