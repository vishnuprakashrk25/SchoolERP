<?php

namespace App\Http\Requests\GeneralSetting;

use App\Http\Requests\ApiFormRequest;

class UpdateGeneralSettingsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'site_title' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'carry_forward_fees_due_days' => 'nullable|integer',
        ];
    }
}

