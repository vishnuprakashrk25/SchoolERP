<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StudentFee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount',
        'status',
        'due_date',
        'paid_amount',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }
}