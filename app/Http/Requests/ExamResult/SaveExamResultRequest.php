<?php

namespace App\Http\Requests\ExamResult;

use App\Http\Requests\ApiFormRequest;

class SaveExamResultRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => 'required|integer',
            'exam_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'marks' => 'required',
        ];
    }
}

