<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'room_types';

    protected $fillable = [
        'name',
        'description',
        'branch_id',
    ];

    public function rooms()
    {
        return $this->hasMany(DormitoryRoom::class, 'room_type_id');
    }
}
