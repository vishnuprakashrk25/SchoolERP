<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'date',
        'status'
    ];
}