<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name',
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

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getNameAttribute($value)
    {
        return $this->full_name;
    }

    public function setNameAttribute($value)
    {
        $this->setFullNameAttribute($value);
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        if (!empty($value) && (empty($this->attributes['first_name']) && empty($this->attributes['last_name']))) {
            $parts = explode(' ', trim($value), 2);
            $this->attributes['first_name'] = $parts[0] ?? '';
            $this->attributes['last_name'] = $parts[1] ?? '';
        }
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $this->formatDateValue($value);
    }

    public function setDateOfJoiningAttribute($value)
    {
        $this->attributes['date_of_joining'] = $this->formatDateValue($value);
    }

    private function formatDateValue($value)
    {
        if (empty($value)) {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        $str = trim((string)$value);
        if ($str === '') {
            return null;
        }
        if (is_numeric($str)) {
            return date('Y-m-d', (int)$str);
        }

        // Clean javascript parenthetical timezone string e.g. " (India Standard Time)" or " (IST)"
        $cleanStr = preg_replace('/\s*\([^)]*\)/', '', $str);

        try {
            return \Illuminate\Support\Carbon::parse($cleanStr)->format('Y-m-d');
        } catch (\Throwable $e) {
            $ts = strtotime($cleanStr);
            if ($ts !== false && $ts > 0) {
                return date('Y-m-d', $ts);
            }
            return null;
        }
    }

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
        return $value;
    }
}