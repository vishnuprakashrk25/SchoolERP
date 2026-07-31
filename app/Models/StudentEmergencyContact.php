<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEmergencyContact extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'relation',
        'phone',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
