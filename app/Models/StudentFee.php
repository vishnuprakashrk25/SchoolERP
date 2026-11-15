<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'fee_structure_id',
        'amount',
        'paid_amount',
        'discount_amount',
        'fine_amount',
        'balance_amount',
        'status',
        'due_date',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function items()
    {
        return $this->hasMany(StudentFeeItem::class, 'student_fee_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_fee_id');
    }

    public function recalculateSummary()
    {
        $this->loadMissing('items');
        if ($this->items->isNotEmpty()) {
            $this->amount = $this->items->sum('amount');
            $this->paid_amount = $this->items->sum('paid_amount');
            $this->discount_amount = $this->items->sum('discount_amount');
            $this->fine_amount = $this->items->sum('fine_amount');
            $this->balance_amount = $this->items->sum('balance_amount');
        } else {
            $net = (float)$this->amount + (float)$this->fine_amount - (float)$this->discount_amount;
            $this->balance_amount = max(0, $net - (float)$this->paid_amount);
        }

        if ((float)$this->balance_amount <= 0) {
            $this->status = ((float)$this->discount_amount > 0 && (float)$this->paid_amount == 0) ? 'waived' : 'paid';
        } elseif ((float)$this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        $this->saveQuietly();
        return $this;
    }
}