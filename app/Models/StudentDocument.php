<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'title',
        'file'
    ];

    public function getFileAttribute($value)
    {
        if (!$value) return null;
        if (str_contains($value, 'storage/student_documents/')) {
            $value = str_replace('storage/student_documents/', 'uploads/student_documents/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) return $value;

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }
}