<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'created_by', 'updated_by', 'school_id', 'academic_id'];
}