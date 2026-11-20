<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'class_id'
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'section_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'section_id');
    }
}