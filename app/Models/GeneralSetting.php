<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
        'school_name',
        'site_title',
        'address',
        'state',
        'phone',
        'email',
        'fees_income_head',
        'school_code',
        'suic_code',
        'zone',
        'category_of_institution',
        'academic_year_id',
        'language',
        'date_format',
        'week_start_day',
        'time_zone',
        'currency',
        'currency_symbol',
        'max_upload_size',
        'multiple_roll_number',
        'promotion_without_exam',
        'subject_attendance_layout',
        'new_fees_module',
        'result_type',
        'student_admission',
        'due_fees_login_restriction',
        'in_news_auto_approval_comment',
        'in_news_can_comment',
        'blog_search',
        'recent_blog',
        'carry_forward_fees_due_days',
        'queue_connection',
        'copyright_text',
    ];

    /**
     * Get the academic year that owns the general settings.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function getLogoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (str_contains($value, 'storage/uploads/')) {
            $value = str_replace('storage/uploads/', 'uploads/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }

    public function getFaviconAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (str_contains($value, 'storage/uploads/')) {
            $value = str_replace('storage/uploads/', 'uploads/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }
}
