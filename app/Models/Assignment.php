<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'section_id',
        'title',
        'description',
        'file'
    ];
}

