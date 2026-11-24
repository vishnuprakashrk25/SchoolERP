<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarkGrade extends Model
{
    use SoftDeletes;

    protected $table = 'mark_grades';

    protected $fillable = [
        'grade_name',
        'gpa',
        'percent_from',
        'percent_to',
        'gpa_from',
        'gpa_to',
        'description',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'gpa'          => 'float',
        'percent_from' => 'float',
        'percent_to'   => 'float',
        'gpa_from'     => 'float',
        'gpa_to'       => 'float',
    ];

    /**
     * Find matching grade based on percentage.
     */
    public static function getGradeForPercent($percent)
    {
        return self::where('percent_from', '<=', $percent)
            ->where('percent_to', '>=', $percent)
            ->first();
    }
}
