<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'routes';

    protected $fillable = [
        'title',
        'fare',
        'branch_id',
    ];

    protected $casts = [
        'fare' => 'float',
    ];

    public function assignedVehicles()
    {
        return $this->hasMany(AssignVehicle::class, 'route_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'assign_vehicles', 'route_id', 'vehicle_id')
            ->whereNull('assign_vehicles.deleted_at')
            ->withTimestamps();
    }
}
