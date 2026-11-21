<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'branch_id',
        'name',
        'full_name',
        'email',
        'phone',
        'photo',
        'password',
        'is_active',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at'
    ];

    protected $appends = [
        'full_name',
        'staff_id',
        'teacher_id',
        'student_id',
        'parent_id',
    ];

    public function getFullNameAttribute($value)
    {
        return $value ?? $this->attributes['name'] ?? null;
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $value;
        }
    }

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google_access_token',
        'google_refresh_token',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function getStaffIdAttribute()
    {
        try {
            if ($this->relationLoaded('staff')) {
                return $this->staff?->id;
            }
            return $this->staff()->value('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getTeacherIdAttribute()
    {
        try {
            $staff = $this->relationLoaded('staff') ? $this->staff : $this->staff()->first();
            if ($staff && (strcasecmp($staff->role, 'teacher') === 0 || $this->hasRole('teacher') || $this->hasRole('Teacher'))) {
                return $staff->id;
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    public function getStudentIdAttribute()
    {
        try {
            if ($this->relationLoaded('student')) {
                return $this->student?->id;
            }
            return $this->student()->value('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getParentIdAttribute()
    {
        try {
            $email = $this->email;
            $phone = $this->phone;
            if (empty($email) && empty($phone)) {
                return null;
            }
            return \App\Models\StudentParent::where(function ($q) use ($email, $phone) {
                if (!empty($email)) {
                    $q->where('father_email', $email)
                      ->orWhere('mother_email', $email)
                      ->orWhere('guardian_email', $email);
                }
                if (!empty($phone)) {
                    $q->orWhere('father_phone', $phone)
                      ->orWhere('mother_phone', $phone)
                      ->orWhere('guardian_phone', $phone);
                }
            })->value('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getPhotoAttribute($value)
    {
        $photo = $value;
        if (empty($photo)) {
            if ($this->relationLoaded('staff') && !empty($this->staff?->photo)) {
                $photo = $this->staff->photo;
            } elseif ($this->relationLoaded('student') && !empty($this->student?->photo)) {
                $photo = $this->student->photo;
            } else {
                $photo = $this->staff?->photo ?? $this->student?->photo ?? null;
            }
        }
        if (!$photo) {
            return null;
        }
        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) {
            return $photo;
        }
        return url($photo);
    }

    public function getPhoneAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        if ($this->relationLoaded('staff') && !empty($this->staff?->phone)) {
            return $this->staff->phone;
        }
        if ($this->relationLoaded('student') && !empty($this->student?->phone)) {
            return $this->student->phone;
        }
        return $this->staff?->phone ?? $this->student?->phone ?? null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
