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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            
            // Logo / Favicon
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // General Settings
            $table->string('school_name')->nullable();
            $table->string('site_title')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('fees_income_head')->nullable();
            $table->string('school_code')->nullable();
            
            // Relations/Identifiers
            $table->unsignedBigInteger('academic_year_id')->nullable();
            
            // Format / Localization
            $table->string('language')->nullable();
            $table->string('date_format')->nullable();
            $table->string('week_start_day')->nullable();
            $table->string('time_zone')->nullable();
            
            // Currency
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            
            // Limits
            $table->string('max_upload_size')->nullable();

            // Options (Enable/Disable / strings)
            $table->string('multiple_roll_number')->nullable();
            $table->string('promotion_without_exam')->nullable();
            $table->string('subject_attendance_layout')->nullable();
            
            // Fees & Results Options
            $table->string('new_fees_module')->nullable();
            $table->string('result_type')->nullable();
            $table->string('student_admission')->nullable();
            $table->string('due_fees_login_restriction')->nullable();
            
            // News & Comments Options
            $table->string('in_news_auto_approval_comment')->nullable();
            $table->string('in_news_can_comment')->nullable();
            
            // Blog Options
            $table->string('blog_search')->nullable();
            $table->string('recent_blog')->nullable();
            
            // Due Days & Queue
            $table->integer('carry_forward_fees_due_days')->nullable();
            $table->string('queue_connection')->nullable();
            $table->string('copyright_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
