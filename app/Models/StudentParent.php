<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentParent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'father_name',
        'father_phone',
        'father_email',
        'father_occupation',
        'father_photo',
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_occupation',
        'mother_photo',
        'guardian_name',
        'guardian_phone',
        'guardian_occupation',
        'guardian_email',
        'guardian_relation',
        'guardian_address',
        'guardian_photo',
        'guardian_is'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getFatherPhotoAttribute($value)
    {
        return $value;
    }

    public function getMotherPhotoAttribute($value)
    {
        return $value;
    }

    public function getGuardianPhotoAttribute($value)
    {
        return $value;
    }
}
