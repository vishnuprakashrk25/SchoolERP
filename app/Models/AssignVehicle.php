<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assign_vehicles';

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'branch_id',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
