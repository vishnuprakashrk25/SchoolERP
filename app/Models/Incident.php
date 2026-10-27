<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incidents';

    protected $fillable = [
        'branch_id',
        'title',
        'point',
        'is_negative',
        'description',
    ];

    protected $casts = [
        'point' => 'integer',
        'is_negative' => 'boolean',
    ];

    public function studentIncidents()
    {
        return $this->hasMany(StudentIncident::class, 'incident_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_incidents', 'incident_id', 'student_id')
            ->withPivot(['id', 'title', 'point', 'is_negative', 'date', 'description', 'assigned_by'])
            ->withTimestamps();
    }
}
