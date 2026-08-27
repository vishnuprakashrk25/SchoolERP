<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'academic_year_id',
        'date',
        'status',
        'note'
    ];

    protected $appends = [
        'roll_no',
        'photo',
        'student_name',
        'student_code',
        'admission_no'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function getRollNoAttribute()
    {
        return $this->student?->roll_no;
    }

    public function getPhotoAttribute()
    {
        return $this->student?->photo;
    }

    public function getStudentNameAttribute()
    {
        return $this->student?->full_name ?? trim(($this->student?->first_name ?? '') . ' ' . ($this->student?->last_name ?? ''));
    }

    public function getStudentCodeAttribute()
    {
        return $this->attributes['student_code'] ?? $this->student?->student_code ?? $this->student?->admission_no;
    }

    public function getAdmissionNoAttribute()
    {
        return $this->attributes['admission_no'] ?? $this->student?->admission_no;
    }
}