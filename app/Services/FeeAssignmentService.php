<?php

namespace App\Services;

use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FeeAssignmentService
{
    /**
     * Resolve target students for a fee structure assignment request.
     */
    public function resolveStudents(FeeStructure $structure, array $filters): Collection
    {
        if (!empty($filters['student_ids'])) {
            return Student::whereIn('id', $filters['student_ids'])->get();
        }

        $classId = $filters['class_id'] ?? $structure->class_id;
        $studentsQuery = Student::where('status', 1);

        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }
        if (!empty($filters['section_id'])) {
            $studentsQuery->where('section_id', $filters['section_id']);
        }
        if (!empty($filters['category_id'])) {
            $studentsQuery->where('category_id', $filters['category_id']);
        }

        return $studentsQuery->get();
    }

    /**
     * Assign a fee structure to students with batched duplicate checks and item inserts.
     *
     * @return array{assigned_count: int, skipped_count: int, student_fee_ids: array<int>}
     */
    public function assignToStudents(
        Collection $students,
        FeeStructure $structure,
        ?int $academicYearId,
        ?string $dueDate,
        ?int $createdBy
    ): array {
        $studentIds = $students->pluck('id')->all();

        // Pre-fetch existing assignments in 1 query instead of querying per student
        $existingStudentIds = StudentFee::where('fee_structure_id', $structure->id)
            ->whereIn('student_id', $studentIds)
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
            ->pluck('student_id')
            ->flip();

        $structure->loadMissing(['items.feeType', 'feeType']);

        $baseAmount = (float) ($structure->total_amount > 0 ? $structure->total_amount : $structure->amount);
        $hasStructureItems = $structure->items->isNotEmpty();

        if ($hasStructureItems) {
            $computedAmount = (float) $structure->items->sum(fn ($item) => (float) $item->amount);
        } else {
            $computedAmount = $baseAmount;
        }

        $computedBalance = max(0.0, $computedAmount);
        $computedStatus = $computedBalance <= 0 ? 'paid' : 'pending';

        $assignedCount = 0;
        $skippedCount = 0;
        $createdRecords = [];
        $itemsToInsert = [];
        $now = now();

        DB::transaction(function () use (
            $students,
            $existingStudentIds,
            $structure,
            $academicYearId,
            $dueDate,
            $createdBy,
            $baseAmount,
            $hasStructureItems,
            $computedAmount,
            $computedBalance,
            $computedStatus,
            $now,
            &$assignedCount,
            &$skippedCount,
            &$createdRecords,
            &$itemsToInsert
        ) {
            foreach ($students as $student) {
                if ($existingStudentIds->has($student->id)) {
                    $skippedCount++;
                    continue;
                }

                $studentFee = StudentFee::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYearId ?? $student->academic_year_id,
                    'fee_structure_id' => $structure->id,
                    'amount' => $computedAmount,
                    'paid_amount' => 0.00,
                    'discount_amount' => 0.00,
                    'fine_amount' => 0.00,
                    'balance_amount' => $computedBalance,
                    'due_date' => $dueDate,
                    'status' => $computedStatus,
                    'created_by' => $createdBy,
                ]);

                if ($hasStructureItems) {
                    foreach ($structure->items as $item) {
                        $itemAmount = (float) $item->amount;
                        $rawItemDue = $item->due_date ?? $dueDate;
                        $itemDueDate = $rawItemDue instanceof \DateTimeInterface
                            ? $rawItemDue->format('Y-m-d')
                            : $rawItemDue;

                        $itemsToInsert[] = [
                            'student_fee_id' => $studentFee->id,
                            'fee_structure_item_id' => $item->id,
                            'fee_type_id' => $item->fee_type_id,
                            'fee_installment_id' => null,
                            'name' => optional($item->feeType)->name ?? ('Fee Item #' . $item->id),
                            'amount' => $itemAmount,
                            'paid_amount' => 0.00,
                            'discount_amount' => 0.00,
                            'fine_amount' => 0.00,
                            'balance_amount' => $itemAmount,
                            'due_date' => $itemDueDate,
                            'status' => 'pending',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                } elseif ($structure->fee_type_id) {
                    $itemsToInsert[] = [
                        'student_fee_id' => $studentFee->id,
                        'fee_structure_item_id' => null,
                        'fee_type_id' => $structure->fee_type_id,
                        'fee_installment_id' => null,
                        'name' => optional($structure->feeType)->name ?? 'Fee Obligation',
                        'amount' => $baseAmount,
                        'paid_amount' => 0.00,
                        'discount_amount' => 0.00,
                        'fine_amount' => 0.00,
                        'balance_amount' => $baseAmount,
                        'due_date' => $dueDate,
                        'status' => 'pending',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $assignedCount++;
                $createdRecords[] = $studentFee->id;
            }

            foreach (array_chunk($itemsToInsert, 500) as $chunk) {
                StudentFeeItem::insert($chunk);
            }
        });

        return [
            'assigned_count' => $assignedCount,
            'skipped_count' => $skippedCount,
            'student_fee_ids' => $createdRecords,
        ];
    }
}

