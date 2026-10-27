<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentIncident extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_incidents';

    protected $fillable = [
        'branch_id',
        'student_id',
        'incident_id',
        'title',
        'point',
        'is_negative',
        'date',
        'description',
        'assigned_by',
    ];

    protected $casts = [
        'point' => 'integer',
        'is_negative' => 'boolean',
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
