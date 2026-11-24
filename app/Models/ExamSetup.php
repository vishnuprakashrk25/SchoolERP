<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSetup extends Model
{
    use SoftDeletes;

    protected $table = 'exam_setups';

    protected $fillable = [
        'exam_title',
        'exam_type_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'pass_mark',
        'total_mark',
        'mark_distributions',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'pass_mark'          => 'float',
        'total_mark'         => 'float',
        'mark_distributions' => 'array',
    ];

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
