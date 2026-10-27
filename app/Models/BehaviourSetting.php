<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviourSetting extends Model
{
    use HasFactory;

    protected $table = 'behaviour_settings';

    protected $fillable = [
        'branch_id',
        'student_comment',
        'parent_comment',
        'student_view',
        'parent_view',
    ];

    protected $casts = [
        'student_comment' => 'boolean',
        'parent_comment'  => 'boolean',
        'student_view'    => 'boolean',
        'parent_view'     => 'boolean',
    ];
}
