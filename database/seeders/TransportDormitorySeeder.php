<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\AssignVehicle;
use App\Models\RoomType;
use App\Models\Dormitory;
use App\Models\DormitoryRoom;

class TransportDormitorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Routes
        $route = Route::firstOrCreate(['title' => 'Test Route'], [
            'fare' => 200.00,
        ]);

        // 2. Vehicles
        $vehicle = Vehicle::firstOrCreate(['vehicle_no' => 'Test Vehicle 23232'], [
            'vehicle_model'  => 'Test Model 4343433',
            'year_made'      => '2007',
            'driver_name'    => 'Jhon Martin',
            'driver_contact' => '7037372929',
            'note'           => 'School Bus',
        ]);

        // 3. Assign Vehicle
        AssignVehicle::firstOrCreate([
            'route_id'   => $route->id,
            'vehicle_id' => $vehicle->id,
        ]);

        // 4. Room Types
        $roomType = RoomType::firstOrCreate(['name' => 'Single'], [
            'description' => 'Test Description',
        ]);
        RoomType::firstOrCreate(['name' => 'Double'], [
            'description' => 'Double Bed Room',
        ]);

        // 5. Dormitory
        $dormitory = Dormitory::firstOrCreate(['name' => 'Test Dormitory'], [
            'type'        => 'Boys',
            'address'     => '25/13, Sukrabad Rd, Tallahbag, Dhaka 1215',
            'intake'      => 200,
            'description' => 'Main Hostel',
        ]);

        // 6. Dormitory Rooms
        DormitoryRoom::firstOrCreate([
            'dormitory_id' => $dormitory->id,
            'room_number'  => '12',
        ], [
            'name'          => '12',
            'room_type_id'  => $roomType->id,
            'number_of_bed' => 21,
            'cost_per_bed'  => 200.00,
            'description'   => 'First Floor Room',
        ]);
    }
}
