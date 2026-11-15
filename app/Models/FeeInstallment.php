<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeInstallment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fee_structure_id',
        'name',
        'due_date',
        'amount',
        'late_fee_type',
        'late_fee_amount',
        'grace_period_days',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
    ];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }
}
