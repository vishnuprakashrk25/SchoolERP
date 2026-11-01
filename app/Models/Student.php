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
        'student_code',
        'admission_date',
        'roll_no',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'class_id',
        'section_id',
        'academic_year_id',
        'category_id',
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

    protected $appends = [
        'full_name',
        'remark_point',
        'remark_points',
        'behaviour_points',
    ];

    protected static function booted()
    {
        static::deleting(function ($student) {
            // Delete the student's personal user login if present
            if ($student->user) {
                try {
                    $student->user->delete();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not delete student user: " . $e->getMessage());
                }
            }

            // Check parent records
            $parent = $student->parents;
            if ($parent) {
                $parentEmails = array_unique(array_filter([
                    $parent->father_email ? trim($parent->father_email) : null,
                    $parent->mother_email ? trim($parent->mother_email) : null,
                    $parent->guardian_email ? trim($parent->guardian_email) : null,
                ]));

                $parentPhones = array_unique(array_filter([
                    $parent->father_phone ? trim($parent->father_phone) : null,
                    $parent->mother_phone ? trim($parent->mother_phone) : null,
                    $parent->guardian_phone ? trim($parent->guardian_phone) : null,
                ]));

                // Delete this student's specific parent record
                try {
                    $parent->delete();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not delete student_parents record: " . $e->getMessage());
                }

                // Check if this parent has other active (non-deleted) students in the school
                $hasOtherStudents = false;
                if (!empty($parentEmails) || !empty($parentPhones)) {
                    $hasOtherStudents = static::where('id', '!=', $student->id)
                        ->whereHas('parents', function ($q) use ($parentEmails, $parentPhones) {
                            $q->where(function ($subQ) use ($parentEmails, $parentPhones) {
                                if (!empty($parentEmails)) {
                                    $subQ->whereIn('father_email', $parentEmails)
                                         ->orWhereIn('mother_email', $parentEmails)
                                         ->orWhereIn('guardian_email', $parentEmails);
                                }
                                if (!empty($parentPhones)) {
                                    $subQ->orWhereIn('father_phone', $parentPhones)
                                         ->orWhereIn('mother_phone', $parentPhones)
                                         ->orWhereIn('guardian_phone', $parentPhones);
                                }
                            });
                        })->exists();
                }

                // If parent has NO other students in the school, clean up the parent User account
                if (!$hasOtherStudents && !empty($parentEmails)) {
                    foreach ($parentEmails as $email) {
                        $user = User::where('email', $email)->first();
                        if ($user) {
                            try {
                                // If user also has other roles (e.g., teacher, staff, admin, driver), keep user and only revoke parent role
                                $otherRoles = $user->roles()->whereNotIn('name', ['parent'])->count();
                                if ($otherRoles > 0) {
                                    $user->removeRole('parent');
                                } else {
                                    $user->delete();
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning("Could not delete or update parent user: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        });
    }

    public function getFullNameAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getNameAttribute()
    {
        return $this->full_name;
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

    public function setAdmissionDateAttribute($value)
    {
        $this->attributes['admission_date'] = $this->formatDateValue($value);
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $this->formatDateValue($value);
    }

    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $this->formatDateValue($value);
    }

    public function setAsOnDateAttribute($value)
    {
        $this->attributes['as_on_date'] = $this->formatDateValue($value);
    }

    public function setDisableDateAttribute($value)
    {
        $this->attributes['disable_date'] = $this->formatDateValue($value);
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

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
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

    public function studentIncidents()
    {
        return $this->hasMany(StudentIncident::class, 'student_id');
    }

    public function behaviourRecords()
    {
        return $this->hasMany(StudentIncident::class, 'student_id');
    }

    public function getRollNoAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return (string) $value;
        }

        if ($this->relationLoaded('studentAcademics')) {
            $academic = $this->studentAcademics->firstWhere('academic_year_id', $this->academic_year_id)
                ?? $this->studentAcademics->sortByDesc('id')->first();
            if ($academic && !empty($academic->roll_no)) {
                return (string) $academic->roll_no;
            }
        } else {
            $academicRollNo = $this->studentAcademics()
                ->when($this->academic_year_id, fn($q) => $q->where('academic_year_id', $this->academic_year_id))
                ->latest('id')
                ->value('roll_no');
            if (!empty($academicRollNo)) {
                return (string) $academicRollNo;
            }
        }

        return null;
    }

    public function getRemarkPointAttribute()
    {
        if ($this->relationLoaded('studentIncidents')) {
            $positive = (int) $this->studentIncidents->where('is_negative', false)->sum('point');
            $negative = (int) $this->studentIncidents->where('is_negative', true)->sum('point');
            return $positive - $negative;
        }

        $positive = (int) $this->studentIncidents()->where('is_negative', false)->sum('point');
        $negative = (int) $this->studentIncidents()->where('is_negative', true)->sum('point');
        return $positive - $negative;
    }

    public function getRemarkPointsAttribute()
    {
        return $this->remark_point;
    }

    public function getRemarksPointAttribute()
    {
        return $this->remark_point;
    }

    public function getBehaviourPointsAttribute()
    {
        return $this->remark_point;
    }

    public function getPhotoAttribute($value)
    {
        return $value;
    }
}