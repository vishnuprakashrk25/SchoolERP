<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function classes()
    {
        return $this->hasMany(Classes::class, 'category_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'category_id');
    }
}