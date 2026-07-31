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
        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_no')->nullable();
            $table->string('role')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('emergency_mobile')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_expert_staff')->default(false);
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            
            // Social Links
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram_url')->nullable();
            
            // Document Info
            $table->string('resume')->nullable();
            $table->string('joining_letter')->nullable();
            $table->string('other_document')->nullable();
            
            // Bank Info
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_name')->nullable();
            
            // Payroll Details
            $table->string('epf_no')->nullable();
            $table->decimal('basic_salary', 15, 2)->nullable();
            $table->string('contract_type')->nullable();
            $table->string('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'staff_no', 'role', 'first_name', 'last_name', 'father_name', 'mother_name',
                'gender', 'dob', 'date_of_joining', 'marital_status', 'emergency_mobile',
                'driving_license', 'photo', 'is_expert_staff', 'current_address', 'permanent_address',
                'qualifications', 'experience', 'facebook_url', 'twitter_url', 'linkedin_url',
                'instagram_url', 'resume', 'joining_letter', 'other_document', 'bank_account_name',
                'bank_account_no', 'bank_name', 'bank_branch_name', 'epf_no', 'basic_salary',
                'contract_type', 'location'
            ]);
        });
    }
};
