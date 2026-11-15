<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function classes()
    {
        return $this->hasMany(Classes::class, 'academic_category_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'academic_category_id');
    }
}
