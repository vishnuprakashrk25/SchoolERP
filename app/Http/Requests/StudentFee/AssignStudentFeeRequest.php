<?php

namespace App\Http\Requests\StudentFee;

use App\Http\Requests\ApiFormRequest;

class AssignStudentFeeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'fee_structure_id' => 'required|integer|exists:fee_structures,id',
            'academic_year_id' => 'nullable|integer',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
            'due_date' => 'nullable|date',
        ];
    }
}

