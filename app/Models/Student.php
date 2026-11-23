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
        'academic_category_id',
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
        return $value ?? $this->attributes['full_name'] ?? null;
    }

    public function getNameAttribute()
    {
        return $this->full_name ?? trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
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

    public function setAdmissionNoAttribute($value)
    {
        if ($this->exists && ($value === null || (is_string($value) && trim($value) === ''))) {
            // On update, never overwrite existing admission_no with null or empty string
            return;
        }
        $this->attributes['admission_no'] = $value;
    }

    public function setClassIdAttribute($value)
    {
        $this->attributes['class_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setSectionIdAttribute($value)
    {
        $this->attributes['section_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setAcademicYearIdAttribute($value)
    {
        $this->attributes['academic_year_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setCategoryIdAttribute($value)
    {
        $this->attributes['category_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setStudentGroupIdAttribute($value)
    {
        $this->attributes['student_group_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setRouteIdAttribute($value)
    {
        $this->attributes['route_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setVehicleIdAttribute($value)
    {
        $this->attributes['vehicle_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setDormitoryIdAttribute($value)
    {
        $this->attributes['dormitory_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setRoomIdAttribute($value)
    {
        $this->attributes['room_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setStudentCodeAttribute($value)
    {
        $this->attributes['student_code'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setDisableReasonAttribute($value)
    {
        $this->attributes['disable_reason'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
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

    public function academicCategory()
    {
        return $this->belongsTo(AcademicCategory::class, 'academic_category_id');
    }

    public function parents()
    {
        return $this->hasOne(StudentParent::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function classes()
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

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
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
        if (isset($this->attributes['remark_point_cache'])) {
            return $this->attributes['remark_point_cache'];
        }

        if ($this->relationLoaded('studentIncidents')) {
            $positive = (int) $this->studentIncidents->where('is_negative', false)->sum('point');
            $negative = (int) $this->studentIncidents->where('is_negative', true)->sum('point');
            return $this->attributes['remark_point_cache'] = ($positive - $negative);
        }

        if (!$this->exists) {
            return 0;
        }

        $calc = $this->studentIncidents()
            ->selectRaw("COALESCE(SUM(CASE WHEN is_negative = 1 THEN -point ELSE point END), 0) as net_points")
            ->value('net_points');

        return $this->attributes['remark_point_cache'] = (int)($calc ?? 0);
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

    public function setAcademicCategoryIdAttribute($value)
    {
        $this->attributes['academic_category_id'] = ($value === '' || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    /**
     * Retrieve siblings of this student based on shared parent contact or parent names.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSiblings()
    {
        if (!$this->relationLoaded('parents')) {
            $this->load('parents');
        }

        $parent = $this->parents;
        if (!$parent) {
            return collect();
        }

        $rawPhones = array_filter([
            $parent->father_phone,
            $parent->mother_phone,
            $parent->guardian_phone,
        ], fn($v) => !empty($v) && trim((string)$v) !== '' && strlen(trim((string)$v)) >= 5);

        $digitPhones = array_map(fn($p) => preg_replace('/\D+/', '', (string)$p), $rawPhones);
        $phones = array_values(array_unique(array_filter(array_merge($rawPhones, $digitPhones), fn($p) => strlen($p) >= 5)));

        $emails = array_values(array_unique(array_filter([
            $parent->father_email,
            $parent->mother_email,
            $parent->guardian_email,
        ], fn($v) => !empty($v) && trim((string)$v) !== '' && str_contains((string)$v, '@'))));

        $fatherName = trim((string)($parent->father_name ?? ''));
        $motherName = trim((string)($parent->mother_name ?? ''));

        if (empty($phones) && empty($emails) && (empty($fatherName) || empty($motherName))) {
            return collect();
        }

        return static::query()
            ->where('students.id', '!=', $this->id)
            ->whereHas('parents', function ($q) use ($phones, $emails, $fatherName, $motherName) {
                $q->where(function ($subQ) use ($phones, $emails, $fatherName, $motherName) {
                    $hasCondition = false;

                    if (!empty($phones)) {
                        $subQ->whereIn('father_phone', $phones)
                             ->orWhereIn('mother_phone', $phones)
                             ->orWhereIn('guardian_phone', $phones);
                        $hasCondition = true;
                    }

                    if (!empty($emails)) {
                        $method = $hasCondition ? 'orWhere' : 'where';
                        $subQ->$method(function ($emailQ) use ($emails) {
                            $emailQ->whereIn('father_email', $emails)
                                   ->orWhereIn('mother_email', $emails)
                                   ->orWhereIn('guardian_email', $emails);
                        });
                        $hasCondition = true;
                    }

                    if (!empty($fatherName) && !empty($motherName)) {
                        $method = $hasCondition ? 'orWhere' : 'where';
                        $subQ->$method(function ($nameQ) use ($fatherName, $motherName) {
                            $nameQ->where('father_name', $fatherName)
                                  ->where('mother_name', $motherName);
                        });
                    }
                });
            })
            ->with([
                'class:id,name',
                'section:id,name',
                'academicYear:id,name'
            ])
            ->select([
                'students.id',
                'students.student_code',
                'students.admission_no',
                'students.roll_no',
                'students.first_name',
                'students.last_name',
                'students.full_name',
                'students.gender',
                'students.photo',
                'students.dob',
                'students.class_id',
                'students.section_id',
                'students.academic_year_id',
                'students.is_disabled',
                'students.status'
            ])
            ->get()
            ->map(function ($sib) {
                $sibArray = $sib->toArray();
                $cName = $sib->class?->name ?? '';
                $sName = $sib->section?->name ?? '';
                $classSection = trim($cName . ($sName ? ' - ' . $sName : ''));

                $sibArray['class_name'] = $cName;
                $sibArray['section_name'] = $sName;
                $sibArray['class_section'] = $classSection;
                $sibArray['class_and_section'] = $classSection;
                $sibArray['academic_year_name'] = $sib->academicYear?->name;
                $sibArray['admission_number'] = $sib->admission_no;
                $sibArray['roll_number'] = (string)($sib->roll_no ?? '');
                $sibArray['remark_points'] = $sib->remark_points;
                $sibArray['remark_point'] = $sib->remark_point;
                $sibArray['student_code'] = $sib->student_code;
                return $sibArray;
            });
    }

    public function getSiblingsAttribute()
    {
        return $this->getSiblings();
    }
}