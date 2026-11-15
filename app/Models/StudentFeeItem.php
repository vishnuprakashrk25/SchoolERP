<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFeeItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_fee_id',
        'fee_structure_item_id',
        'fee_type_id',
        'fee_installment_id',
        'name',
        'amount',
        'paid_amount',
        'discount_amount',
        'fine_amount',
        'balance_amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'student_fee_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }

    public function feeInstallment()
    {
        return $this->belongsTo(FeeInstallment::class, 'fee_installment_id');
    }

    public function feeStructureItem()
    {
        return $this->belongsTo(FeeStructureItem::class, 'fee_structure_item_id');
    }

    public function paymentItems()
    {
        return $this->hasMany(FeePaymentItem::class, 'student_fee_item_id');
    }

    public function recalculateBalances()
    {
        $netPayable = (float)$this->amount + (float)$this->fine_amount - (float)$this->discount_amount;
        $balance = max(0, $netPayable - (float)$this->paid_amount);
        $this->balance_amount = $balance;

        if ($this->status !== 'waived') {
            if ($balance <= 0) {
                $this->status = ((float)$this->discount_amount > 0 && (float)$this->paid_amount == 0) ? 'waived' : 'paid';
            } elseif ((float)$this->paid_amount > 0) {
                $this->status = 'partial';
            } else {
                $this->status = 'pending';
            }
        }

        $this->saveQuietly();
        return $this;
    }
}
