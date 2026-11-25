<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Upgrade routes table
        if (Schema::hasTable('routes')) {
            Schema::table('routes', function (Blueprint $table) {
                if (Schema::hasColumn('routes', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                }
            });
        } else {
            Schema::create('routes', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->decimal('fare', 10, 2)->default(0.00);
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Upgrade vehicles table
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                if (!Schema::hasColumn('vehicles', 'year_made')) {
                    $table->string('year_made')->nullable()->after('vehicle_model');
                }
                if (!Schema::hasColumn('vehicles', 'driver_id')) {
                    $table->unsignedBigInteger('driver_id')->nullable()->after('year_made')->index();
                }
                if (!Schema::hasColumn('vehicles', 'note')) {
                    $table->text('note')->nullable()->after('driver_contact');
                }
                if (Schema::hasColumn('vehicles', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                }
            });
        } else {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('vehicle_no');
                $table->string('vehicle_model')->nullable();
                $table->string('year_made')->nullable();
                $table->unsignedBigInteger('driver_id')->nullable()->index();
                $table->string('driver_name')->nullable();
                $table->string('driver_license')->nullable();
                $table->string('driver_contact')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Assign Vehicles (Link Route to Vehicles)
        if (!Schema::hasTable('assign_vehicles')) {
            Schema::create('assign_vehicles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('route_id')->index();
                $table->unsignedBigInteger('vehicle_id')->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Room Types
        if (!Schema::hasTable('room_types')) {
            Schema::create('room_types', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. Single, Double, Triple
                $table->text('description')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 5. Upgrade dormitories table
        if (Schema::hasTable('dormitories')) {
            Schema::table('dormitories', function (Blueprint $table) {
                if (!Schema::hasColumn('dormitories', 'address')) {
                    $table->text('address')->nullable()->after('type');
                }
                if (!Schema::hasColumn('dormitories', 'intake')) {
                    $table->integer('intake')->nullable()->after('address');
                }
                if (!Schema::hasColumn('dormitories', 'description')) {
                    $table->text('description')->nullable()->after('intake');
                }
                if (Schema::hasColumn('dormitories', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                }
            });
        } else {
            Schema::create('dormitories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->nullable(); // Boys, Girls, Mixed
                $table->text('address')->nullable();
                $table->integer('intake')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 6. Upgrade dormitory_rooms table
        if (Schema::hasTable('dormitory_rooms')) {
            Schema::table('dormitory_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('dormitory_rooms', 'room_number')) {
                    $table->string('room_number')->nullable()->after('dormitory_id');
                }
                if (!Schema::hasColumn('dormitory_rooms', 'room_type_id')) {
                    $table->unsignedBigInteger('room_type_id')->nullable()->after('room_number')->index();
                }
                if (!Schema::hasColumn('dormitory_rooms', 'description')) {
                    $table->text('description')->nullable()->after('cost_per_bed');
                }
                if (Schema::hasColumn('dormitory_rooms', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                }
            });
        } else {
            Schema::create('dormitory_rooms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dormitory_id')->index();
                $table->string('room_number')->nullable();
                $table->string('name')->nullable();
                $table->unsignedBigInteger('room_type_id')->nullable()->index();
                $table->integer('number_of_bed')->default(1);
                $table->decimal('cost_per_bed', 10, 2)->default(0.00);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('assign_vehicles');
    }
};
