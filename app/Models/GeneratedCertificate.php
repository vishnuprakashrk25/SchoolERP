<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GeneratedCertificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'certificate_template_id',
        'file'
    ];
}