<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dormitory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dormitories';

    protected $fillable = [
        'name',
        'type',
        'address',
        'intake',
        'description',
        'branch_id',
    ];

    protected $casts = [
        'intake' => 'integer',
    ];

    public function rooms()
    {
        return $this->hasMany(DormitoryRoom::class, 'dormitory_id');
    }
}
