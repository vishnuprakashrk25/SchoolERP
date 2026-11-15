<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePaymentItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'student_fee_item_id',
        'amount',
        'discount_amount',
        'fine_amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function studentFeeItem()
    {
        return $this->belongsTo(StudentFeeItem::class, 'student_fee_item_id');
    }
}
