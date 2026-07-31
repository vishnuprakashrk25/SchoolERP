<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Builder;

class Teacher extends Staff
{
    protected $table = 'staff';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Automatically scope queries to only include teachers
        static::addGlobalScope('teacher', function (Builder $builder) {
            $builder->where('role', 'Teacher')->orWhere('role', 'teacher');
        });

        // Automatically set role when creating a new Teacher using this model
        static::creating(function ($teacher) {
            if (empty($teacher->role)) {
                $teacher->role = 'Teacher';
            }
        });
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'teacher_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'teacher_id');
    }
}
