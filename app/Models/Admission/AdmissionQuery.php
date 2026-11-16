<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;

class AdmissionQuery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'description',
        'message',
        'date',
        'next_follow_up_date',
        'follow_up_date',
        'assigned',
        'reference',
        'source',
        'class_id',
        'no_of_child',
        'status',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
