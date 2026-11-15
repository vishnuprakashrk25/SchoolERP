<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_no',
        'receipt_no',
        'student_id',
        'student_fee_id',
        'academic_year_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'bank_name',
        'cheque_date',
        'discount_amount',
        'fine_amount',
        'total_paid',
        'note',
        'collected_by',
        'status',
        'reversal_reason',
        'reversed_at',
        'reversed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'reversed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'student_fee_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function items()
    {
        return $this->hasMany(FeePaymentItem::class, 'payment_id');
    }

    public static function generatePaymentNo(): string
    {
        $prefix = 'PAY-' . date('Ym') . '-';
        $latest = static::where('payment_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('payment_no');

        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $num = (int)$matches[1] + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNo(): string
    {
        $prefix = 'REC-' . date('Ym') . '-';
        $latest = static::where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receipt_no');

        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $num = (int)$matches[1] + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}