<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'follow_up_date',
        'source',
        'status',
    ];
}
