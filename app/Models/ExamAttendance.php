<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'exam_attendances';

    protected $fillable = [
        'exam_id',
        'class_id',
        'section_id',
        'subject_id',
        'student_id',
        'status',
        'note',
        'date',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_id');
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
