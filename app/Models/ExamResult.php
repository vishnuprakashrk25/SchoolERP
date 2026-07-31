<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'exam_id',
        'subject_id',
        'marks'
    ];
}