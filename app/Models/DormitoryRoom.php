<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DormitoryRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dormitory_rooms';

    protected $fillable = [
        'dormitory_id',
        'room_number',
        'name',
        'room_type_id',
        'number_of_bed',
        'cost_per_bed',
        'description',
        'branch_id',
    ];

    protected $casts = [
        'number_of_bed' => 'integer',
        'cost_per_bed'  => 'float',
    ];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
}
