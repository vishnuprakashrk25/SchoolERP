<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeworkSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'homework_submissions';

    protected $fillable = [
        'homework_id',
        'student_id',
        'submission_date',
        'submission_file',
        'student_notes',
        'marks',
        'status',
        'evaluated_by',
        'evaluation_date',
        'description',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'evaluation_date' => 'date:Y-m-d',
        'marks'           => 'float',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
