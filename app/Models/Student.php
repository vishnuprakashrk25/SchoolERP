<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'admission_no',
        'admission_date',
        'roll_no',
        'first_name',
        'last_name',
        'email',
        'phone',
        'class_id',
        'section_id',
        'dob',
        'gender',
        'religion',
        'caste',
        'blood_group',
        'height',
        'weight',
        'as_on_date',
        'address',
        'nationality',
        'current_address',
        'permanent_address',
        'bank_account_no',
        'bank_name',
        'ifsc_code',
        'national_id_no',
        'local_id_no',
        'birth_certificate_no',
        'apaar_id',
        'aadharshila_no',
        'pen_no',
        'previous_school_name',
        'previous_qualification',
        'previous_school_details',
        'note',
        'photo',
        'emergency_phone',
        'alternate_phone',
        'student_group_id',
        'medical_history',
        'is_disabled',
        'disable_reason',
        'disable_date',
        'route_id',
        'vehicle_id',
        'dormitory_id',
        'room_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(StudentCategory::class, 'category_id');
    }

    public function parents()
    {
        return $this->hasOne(StudentParent::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\StudentGroup::class, 'student_group_id');
    }

    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }

    public function dormitory()
    {
        return $this->belongsTo(\App\Models\Dormitory::class);
    }

    public function room()
    {
        return $this->belongsTo(\App\Models\DormitoryRoom::class, 'room_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(StudentEmergencyContact::class);
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (str_contains($value, 'storage/student_photos/')) {
            $value = str_replace('storage/student_photos/', 'uploads/student_photos/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }
}