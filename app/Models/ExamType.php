<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends Model
{
    use SoftDeletes;

    protected $table = 'exam_types';

    protected $fillable = [
        'name',
        'is_group',
        'status',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'status'   => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function setups()
    {
        return $this->hasMany(ExamSetup::class, 'exam_type_id');
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class, 'exam_type_id');
    }
}
