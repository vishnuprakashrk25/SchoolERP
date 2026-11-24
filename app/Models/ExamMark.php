<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMark extends Model
{
    use SoftDeletes;

    protected $table = 'exam_marks';

    protected $fillable = [
        'exam_id',
        'exam_setup_id',
        'class_id',
        'section_id',
        'subject_id',
        'student_id',
        'marks_distribution',
        'total_marks',
        'grade',
        'gpa',
        'is_absent',
        'remarks',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'marks_distribution' => 'array',
        'total_marks'        => 'float',
        'gpa'                => 'float',
        'is_absent'          => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_id');
    }

    public function examSetup()
    {
        return $this->belongsTo(ExamSetup::class, 'exam_setup_id');
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

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
