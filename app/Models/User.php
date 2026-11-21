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
        'profile_id',
        'profile_type',
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

    public function getStaffIdAttribute()
    {
        if ($this->relationLoaded('staff') && $this->staff) {
            return $this->staff->id;
        }
        $id = $this->staff()->value('id');
        if ($id) {
            return $id;
        }
        if (!empty($this->email)) {
            $staff = \App\Models\Staff::where('email', $this->email)->first();
            if ($staff) {
                if (empty($staff->user_id)) {
                    $staff->update(['user_id' => $this->id]);
                }
                return $staff->id;
            }
        }
        if (!empty($this->attributes['phone'] ?? null)) {
            $staff = \App\Models\Staff::where('phone', $this->attributes['phone'])->first();
            if ($staff) {
                if (empty($staff->user_id)) {
                    $staff->update(['user_id' => $this->id]);
                }
                return $staff->id;
            }
        }
        return null;
    }

    public function getTeacherIdAttribute()
    {
        $staff = $this->relationLoaded('staff') ? $this->staff : $this->staff()->first();
        if (!$staff && !empty($this->email)) {
            $staff = \App\Models\Staff::where('email', $this->email)->first();
        }
        if (!$staff && !empty($this->attributes['phone'] ?? null)) {
            $staff = \App\Models\Staff::where('phone', $this->attributes['phone'])->first();
        }

        if ($staff) {
            $isTeacherRole = strcasecmp($staff->role ?? '', 'teacher') === 0 
                || $this->hasRole('teacher') 
                || $this->hasRole('Teacher');
            if ($isTeacherRole) {
                return $staff->id;
            }
        } elseif ($this->hasRole('teacher') || $this->hasRole('Teacher')) {
            return null;
        }
        return null;
    }

    public function getStudentIdAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->id;
        }
        $id = $this->student()->value('id');
        if ($id) {
            return $id;
        }
        if (!empty($this->email)) {
            $student = \App\Models\Student::where('email', $this->email)->first();
            if ($student) {
                if (empty($student->user_id)) {
                    $student->update(['user_id' => $this->id]);
                }
                return $student->id;
            }
        }
        if (!empty($this->attributes['phone'] ?? null)) {
            $student = \App\Models\Student::where('phone', $this->attributes['phone'])->first();
            if ($student) {
                if (empty($student->user_id)) {
                    $student->update(['user_id' => $this->id]);
                }
                return $student->id;
            }
        }
        return null;
    }

    public function getParentIdAttribute()
    {
        $email = $this->email;
        $phone = $this->attributes['phone'] ?? null;

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
    }

    public function getProfileIdAttribute()
    {
        if ($this->teacher_id) {
            return $this->teacher_id;
        }
        if ($this->staff_id) {
            return $this->staff_id;
        }
        if ($this->student_id) {
            return $this->student_id;
        }
        if ($this->parent_id) {
            return $this->parent_id;
        }
        return $this->id;
    }

    public function getProfileTypeAttribute()
    {
        if ($this->teacher_id) {
            return 'teacher';
        }
        if ($this->staff_id) {
            return 'staff';
        }
        if ($this->student_id) {
            return 'student';
        }
        if ($this->parent_id) {
            return 'parent';
        }
        return 'user';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
