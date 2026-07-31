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
        Schema::table('students', function (Blueprint $table) {
            $table->string('emergency_phone')->nullable()->after('phone');
            $table->string('alternate_phone')->nullable()->after('emergency_phone');
            $table->foreignId('student_group_id')->nullable()->after('category_id')->constrained('student_groups')->onDelete('set null');
            $table->text('medical_history')->nullable()->after('birth_certificate_no');
            
            // Disabled status
            $table->boolean('is_disabled')->default(false)->after('note');
            $table->string('disable_reason')->nullable()->after('is_disabled');
            $table->date('disable_date')->nullable()->after('disable_reason');
            
            // Transport & Dormitory
            $table->foreignId('route_id')->nullable()->after('disable_date')->constrained('routes')->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->after('route_id')->constrained('vehicles')->onDelete('set null');
            $table->foreignId('dormitory_id')->nullable()->after('vehicle_id')->constrained('dormitories')->onDelete('set null');
            $table->foreignId('room_id')->nullable()->after('dormitory_id')->constrained('dormitory_rooms')->onDelete('set null');
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->string('father_email')->nullable()->after('father_phone');
            $table->string('mother_email')->nullable()->after('mother_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['student_group_id']);
            $table->dropForeign(['route_id']);
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['dormitory_id']);
            $table->dropForeign(['room_id']);
            $table->dropColumn([
                'emergency_phone', 'alternate_phone', 'student_group_id', 
                'medical_history', 'is_disabled', 'disable_reason', 'disable_date',
                'route_id', 'vehicle_id', 'dormitory_id', 'room_id'
            ]);
        });

        Schema::table('student_parents', function (Blueprint $table) {
            $table->dropColumn(['father_email', 'mother_email']);
        });
    }
};
