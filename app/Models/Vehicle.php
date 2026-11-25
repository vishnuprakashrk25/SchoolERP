<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'vehicle_no',
        'vehicle_model',
        'year_made',
        'driver_id',
        'driver_name',
        'driver_license',
        'driver_contact',
        'note',
        'branch_id',
    ];

    public function driver()
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    public function assignedRoutes()
    {
        return $this->hasMany(AssignVehicle::class, 'vehicle_id');
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'assign_vehicles', 'vehicle_id', 'route_id')
            ->whereNull('assign_vehicles.deleted_at')
            ->withTimestamps();
    }
}
