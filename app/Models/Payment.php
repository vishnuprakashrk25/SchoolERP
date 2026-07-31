<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_fee_id',
        'amount',
        'payment_date'
    ];
}