<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time'
    ];
}