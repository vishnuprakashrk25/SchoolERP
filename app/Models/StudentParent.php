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
        if (!$value) return null;
        if (str_contains($value, 'storage/parent_photos/')) {
            $value = str_replace('storage/parent_photos/', 'uploads/parent_photos/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) return $value;
        
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }

    public function getMotherPhotoAttribute($value)
    {
        if (!$value) return null;
        if (str_contains($value, 'storage/parent_photos/')) {
            $value = str_replace('storage/parent_photos/', 'uploads/parent_photos/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) return $value;

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }

    public function getGuardianPhotoAttribute($value)
    {
        if (!$value) return null;
        if (str_contains($value, 'storage/parent_photos/')) {
            $value = str_replace('storage/parent_photos/', 'uploads/parent_photos/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) return $value;

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }
}
