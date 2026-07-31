<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSubject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}