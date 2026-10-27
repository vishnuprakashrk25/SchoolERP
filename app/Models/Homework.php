<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homework extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'homeworks';

    protected $fillable = [
        'branch_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'subject_id',
        'created_by',
        'evaluated_by',
        'homework_date',
        'submission_date',
        'evaluation_date',
        'marks',
        'description',
        'file',
        'status',
    ];

    protected $casts = [
        'homework_date'   => 'date:Y-m-d',
        'submission_date' => 'date:Y-m-d',
        'evaluation_date' => 'date:Y-m-d',
        'marks'           => 'float',
    ];

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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class, 'homework_id');
    }
}
