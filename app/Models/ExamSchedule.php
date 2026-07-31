<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_id',
        'class_id',
        'subject_id',
        'exam_date'
    ];
}