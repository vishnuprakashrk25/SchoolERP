<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'branch_id',
        'academic_year_id',
        'category_id',
        'academic_category_id'
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicCategory()
    {
        return $this->belongsTo(AcademicCategory::class, 'academic_category_id');
    }

    public function category()
    {
        return $this->academicCategory();
    }

    public function setCategoryIdAttribute($value)
    {
        $val = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
        $this->attributes['category_id'] = $val;
        if (!isset($this->attributes['academic_category_id']) || empty($this->attributes['academic_category_id'])) {
            $this->attributes['academic_category_id'] = $val;
        }
    }

    public function setAcademicCategoryIdAttribute($value)
    {
        $val = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
        $this->attributes['academic_category_id'] = $val;
        if (!isset($this->attributes['category_id']) || empty($this->attributes['category_id'])) {
            $this->attributes['category_id'] = $val;
        }
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'class_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }
}