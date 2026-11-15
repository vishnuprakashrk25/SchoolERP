<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'class_id',
        'section_id',
        'category_id',
        'academic_category_id',
        'academic_year_id',
        'fee_type_id',
        'amount',
        'total_amount',
        'description',
        'school_id',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function category()
    {
        return $this->belongsTo(StudentCategory::class, 'category_id');
    }

    public function academicCategory()
    {
        return $this->belongsTo(AcademicCategory::class, 'academic_category_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }

    public function items()
    {
        return $this->hasMany(FeeStructureItem::class, 'fee_structure_id');
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class, 'fee_structure_id');
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class, 'fee_structure_id');
    }
}