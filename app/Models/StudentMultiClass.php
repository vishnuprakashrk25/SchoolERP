<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMultiClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'section_id'
    ];
}