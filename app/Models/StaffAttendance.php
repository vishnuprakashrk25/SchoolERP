<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'staff_id',
        'date',
        'status',
        'note'
    ];

    protected $appends = [
        'photo'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function getPhotoAttribute()
    {
        return $this->staff?->photo;
    }
}