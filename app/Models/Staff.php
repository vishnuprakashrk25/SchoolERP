<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'phone',
        'designation',
        'category',
        'staff_code',
        'role',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'gender',
        'dob',
        'date_of_joining',
        'marital_status',
        'emergency_mobile',
        'driving_license',
        'photo',
        'is_expert_staff',
        'current_address',
        'permanent_address',
        'qualifications',
        'experience',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'resume',
        'joining_letter',
        'other_document',
        'bank_account_name',
        'bank_account_no',
        'bank_name',
        'bank_branch_name',
        'epf_no',
        'basic_salary',
        'contract_type',
        'location',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStaffNoAttribute()
    {
        return $this->attributes['staff_code'] ?? null;
    }

    public function setStaffNoAttribute($value)
    {
        $this->attributes['staff_code'] = $value;
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) return null;
        if (str_contains($value, 'storage/staff_documents/')) {
            $value = str_replace('storage/staff_documents/', 'uploads/staff_photos/', $value);
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) return $value;
        
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'uploads/') && !str_starts_with($path, 'public/')) {
            $path = 'public/' . $path;
        }
        return url($path);
    }
}