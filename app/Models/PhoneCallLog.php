<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneCallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'date',
        'description',
        'next_follow_up_date',
        'call_duration',
        'note',
        'call_type',
    ];
}
